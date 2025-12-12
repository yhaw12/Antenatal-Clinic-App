<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\CallLog;
use App\Models\Visit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportWebController extends Controller
{
    /**
     * Display the default report page (Defaults to Today/This Month).
     */
    public function index(Request $request)
    {
        // Default to this month if no dates provided
        $from = $request->input('from') ? Carbon::parse($request->input('from')) : Carbon::now()->startOfMonth();
        $to   = $request->input('to') ? Carbon::parse($request->input('to')) : Carbon::now()->endOfMonth();
        $status = $request->input('status');

        return $this->generateReportResponse($from, $to, $status);
    }

    /**
     * Handle the report generation form submission.
     */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'from'   => 'required|date',
            'to'     => 'required|date',
            'status' => 'nullable|string|in:scheduled,present,missed,cancelled',
            'month1' => 'nullable|date',
            'month2' => 'nullable|date',
        ]);

        // 1. Handle Comparison Mode (Month vs Month)
        if ($request->filled('month1') && $request->filled('month2')) {
            return $this->generateComparisonResponse($data);
        }

        // 2. Handle Standard Date Range Mode
        $from = Carbon::parse($data['from'])->startOfDay();
        $to   = Carbon::parse($data['to'])->endOfDay();

        if ($from->gt($to)) {
            return back()->withErrors(['from' => 'Start date cannot be after end date.'])->withInput();
        }

        return $this->generateReportResponse($from, $to, $data['status'] ?? null);
    }

    /**
     * Helper: Generates the standard view response.
     */
    private function generateReportResponse(Carbon $from, Carbon $to, ?string $status)
    {
        $report = $this->getReportData($from, $to, $status);

        return view('reports.index', [
            'from'       => $from->toDateString(),
            'to'         => $to->toDateString(),
            'status'     => $status,
            'appts'      => $report['appts'],
            'kpis'       => $report['kpis'],
            'chart'      => $report['chart'], // Contains labels, counts, and chartTitle
            'chartTitle' => $report['chartTitle'], // Pass explicitly for easier view access
            'callStats'  => $report['callStats'],
            'comparison' => null,
        ]);
    }

    /**
     * Helper: Generates the comparison view response.
     */
    private function generateComparisonResponse(array $data)
    {
        $m1Start = Carbon::parse($data['month1'])->startOfMonth();
        $m1End   = $m1Start->copy()->endOfMonth();
        
        $m2Start = Carbon::parse($data['month2'])->startOfMonth();
        $m2End   = $m2Start->copy()->endOfMonth();

        $m1Data = $this->getReportData($m1Start, $m1End, $data['status'] ?? null);
        $m2Data = $this->getReportData($m2Start, $m2End, $data['status'] ?? null);

        $total1 = $m1Data['kpis']['total'];
        $total2 = $m2Data['kpis']['total'];
        $change = $total2 - $total1;
        
        $pctChange = $total1 > 0 ? round(($change / $total1) * 100) : ($total2 > 0 ? 100 : 0);
        $direction = $change > 0 ? 'increased' : ($change < 0 ? 'decreased' : 'stable');

        $comparison = [
            'month1'         => $data['month1'],
            'month2'         => $data['month2'],
            'month1_data'    => $m1Data['kpis'],
            'month2_data'    => $m2Data['kpis'],
            'change_summary' => "Appointments {$direction} by {$pctChange}% ({$total1} vs {$total2}).",
            'chart_labels'   => [$m1Start->format('M Y'), $m2Start->format('M Y')],
            'chart_datasets' => [
                [
                    'label' => 'Total Appointments',
                    'data'  => [$total1, $total2],
                    'backgroundColor' => ['rgba(148, 163, 184, 0.7)', 'rgba(59, 130, 246, 0.8)'],
                ]
            ]
        ];

        $emptyPaginator = new LengthAwarePaginator(collect([]), 0, 25);

        return view('reports.index', [
            'from'       => $data['from'],
            'to'         => $data['to'],
            'status'     => $data['status'] ?? null,
            'appts'      => $emptyPaginator,
            'kpis'       => ['total' => $total1 + $total2, 'present' => 0, 'notArrived' => 0, 'rate' => 0, 'new' => 0, 'review' => 0, 'referrals' => 0, 'cancelled' => 0],
            'chart'      => ['labels' => [], 'counts' => []],
            'chartTitle' => 'Comparison Mode',
            'callStats'  => [],
            'comparison' => $comparison,
        ]);
    }

    /**
     * Core Logic: Fetches Data with Dynamic Granularity (Month vs Day).
     */
    private function getReportData(Carbon $from, Carbon $to, ?string $status): array
    {
        // 1. Identify Date Column
        $dateColumn = Schema::hasColumn('appointments', 'scheduled_date') ? 'scheduled_date' : 'date';

        // 2. Build Base Query
        $baseQuery = Appointment::query()->whereBetween($dateColumn, [$from->toDateString(), $to->toDateString()]);

        if (!empty($status)) {
            if ($status === 'present') $baseQuery->whereIn('status', ['queued', 'in_room', 'seen', 'present']);
            elseif ($status === 'missed') $baseQuery->where('status', 'missed');
            elseif ($status === 'scheduled') $baseQuery->where('status', 'scheduled');
            elseif ($status === 'cancelled') $baseQuery->where('status', 'cancelled');
        }

        // 3. Determine Chart Granularity
        // If range > 90 days, switch to Monthly view
        $diffInDays = $from->diffInDays($to);
        $isMonthly = $diffInDays > 90;

        $labels = [];
        $counts = [];
        $chartTitle = "Daily Appointments Trend"; // Default title

        // Clone query for chart to avoid modifying original
        $chartQuery = clone $baseQuery;

        if ($isMonthly) {
            // --- MONTHLY GROUPING ---
            $chartTitle = "Monthly Appointments Trend";

            // Group by Year-Month (e.g., 2025-01)
            $monthlyCounts = $chartQuery->select(
                DB::raw("DATE_FORMAT($dateColumn, '%Y-%m') as date_key"),
                DB::raw('count(*) as total')
            )
            ->groupBy('date_key')
            ->pluck('total', 'date_key')
            ->toArray();

            // Loop through months to fill gaps
            $current = $from->copy()->startOfMonth();
            $endMonth = $to->copy()->startOfMonth();

            while ($current->lte($endMonth)) {
                $key = $current->format('Y-m');
                // Label: "Jan 2025"
                $labels[] = $current->format('M Y'); 
                $counts[] = $monthlyCounts[$key] ?? 0;
                $current->addMonth();
            }

        } else {
            // --- DAILY GROUPING ---
            $chartTitle = "Daily Appointments Trend";

            $dailyCounts = $chartQuery->select(
                DB::raw("DATE($dateColumn) as date_key"),
                DB::raw('count(*) as total')
            )
            ->groupBy('date_key')
            ->pluck('total', 'date_key')
            ->toArray();

            // Loop through days to fill gaps
            $current = $from->copy();
            while ($current->lte($to)) {
                $key = $current->format('Y-m-d');
                // Label: "25/12/2024"
                $labels[] = $current->format('d/m/Y'); 
                $counts[] = $dailyCounts[$key] ?? 0;
                $current->addDay();
            }
        }

        // 4. Fetch Paginated List
        $listQuery = clone $baseQuery;
        $listQuery->with('patient')->orderBy($dateColumn, 'desc');
        if (Schema::hasColumn('appointments', 'time')) {
            $listQuery->orderBy('time', 'desc');
        }
        $appts = $listQuery->paginate(20)->appends(request()->query());

        // 5. Calculate KPIs
        $total = (clone $baseQuery)->count();
        $present = (clone $baseQuery)->whereIn('status', ['queued', 'in_room', 'seen', 'present'])->count();
        $notArrived = (clone $baseQuery)->whereIn('status', ['missed', 'absent'])->count();
        $attendanceRate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        // Workload Logic
        $allInPeriod = (clone $baseQuery)->with('patient:id,created_at')->get();
        $newVisits = $allInPeriod->filter(function($appt) {
            return $appt->patient && $appt->patient->created_at->isSameDay($appt->date);
        })->count();
        $reviews = max(0, $total - $newVisits);

        // Referrals & Cancellations
        $referrals = Visit::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->whereNotNull('referral_to')->count();
        $cancelled = (clone $baseQuery)->where('status', 'cancelled')->count();

        // 6. Call Stats
        $callStats = CallLog::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->select('result', DB::raw('count(*) as total'))
            ->groupBy('result')
            ->pluck('total', 'result')
            ->toArray();

        return [
            'appts' => $appts,
            'kpis'  => [
                'total'      => $total,
                'present'    => $present,
                'notArrived' => $notArrived,
                'rate'       => $attendanceRate,
                'new'        => $newVisits,
                'review'     => $reviews,
                'referrals'  => $referrals,
                'cancelled'  => $cancelled
            ],
            'chart'      => ['labels' => $labels, 'counts' => $counts],
            'chartTitle' => $chartTitle, // Passing title back
            'callStats'  => $callStats,
        ];
    }
}