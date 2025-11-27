<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\CallLog;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportWebController extends Controller
{
    /**
     * Display the default report page (Defaults to Today).
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        return $this->generateReportResponse($today->startOfDay(), $today->endOfDay(), null);
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
     * Helper: Generates the standard view response with advanced metrics.
     */
    private function generateReportResponse(Carbon $from, Carbon $to, ?string $status)
    {
        // Get all data using the shared logic
        $report = $this->getReportData($from, $to, $status);

        return view('reports.index', [
            'from'       => $from->toDateString(),
            'to'         => $to->toDateString(),
            'status'     => $status,
            'appts'      => $report['appts'],
            'kpis'       => $report['kpis'],
            'chart'      => $report['chart'],
            'callStats'  => $report['callStats'], // Advanced Metric
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

        // Fetch data for both months
        $m1Data = $this->getReportData($m1Start, $m1End, $data['status'] ?? null);
        $m2Data = $this->getReportData($m2Start, $m2End, $data['status'] ?? null);

        // Calculate Growth/Decline
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
            'change_summary' => "Appointments {$direction} by {$pctChange}% ({$total1} vs {$total2}) between " . $m1Start->format('M Y') . " and " . $m2Start->format('M Y') . ".",
            'chart_labels'   => [$m1Start->format('M Y'), $m2Start->format('M Y')],
            'chart_datasets' => [
                [
                    'label' => 'Total Appointments',
                    'data'  => [$total1, $total2],
                    'backgroundColor' => ['rgba(148, 163, 184, 0.7)', 'rgba(59, 130, 246, 0.8)'],
                ]
            ]
        ];

        // Return empty pagination for lists when comparing
        $emptyPaginator = new LengthAwarePaginator(collect([]), 0, 25);

        return view('reports.index', [
            'from'       => $data['from'],
            'to'         => $data['to'],
            'status'     => $data['status'] ?? null,
            'appts'      => $emptyPaginator,
            'kpis'       => ['total' => $total1 + $total2, 'present' => 0, 'notArrived' => 0, 'rate' => 0],
            'chart'      => ['labels' => [], 'counts' => []],
            'callStats'  => [],
            'comparison' => $comparison,
        ]);
    }

    /**
     * Core Logic: Fetches Appointments, KPIs, Charts, and Call Stats.
     */
    private function getReportData(Carbon $from, Carbon $to, ?string $status): array
    {
        // 1. Identify Date Column
        $dateColumn = Schema::hasColumn('appointments', 'scheduled_date') ? 'scheduled_date' : 'date';

        // 2. Build Base Query
        $baseQuery = Appointment::query()->whereBetween($dateColumn, [$from->toDateString(), $to->toDateString()]);

        // 3. Apply Filters
        if (!empty($status)) {
            if ($status === 'present') {
                $baseQuery->whereIn('status', ['queued', 'in_room', 'seen', 'present']);
            } elseif ($status === 'missed') {
                $baseQuery->where('status', 'missed');
            } elseif ($status === 'scheduled') {
                $baseQuery->where('status', 'scheduled');
            } elseif ($status === 'cancelled') {
                $baseQuery->where('status', 'cancelled');
            }
        }

        // 4. Fetch Paginated List
        $listQuery = clone $baseQuery;
        $listQuery->with('patient')->orderBy($dateColumn, 'desc'); // Newest first
        if (Schema::hasColumn('appointments', 'time')) {
            $listQuery->orderBy('time', 'desc');
        }
        $appts = $listQuery->paginate(25)->appends(request()->query());

        // 5. Calculate Advanced KPIs
        $total = (clone $baseQuery)->count();
        
        // "Present" count logic (supports multiple status variations)
        $present = (clone $baseQuery)->whereIn('status', ['queued', 'in_room', 'seen', 'present'])->count();
        
        // "Missed" count
        $notArrived = (clone $baseQuery)->whereIn('status', ['missed', 'absent'])->count();
        
        // Fallback math if status isn't perfectly updated yet
        if ($present + $notArrived < $total && empty($status)) {
             // If user hasn't filtered, assume remaining are scheduled/pending
             // You can optionally calculate $notArrived = $total - $present; here if you prefer strict math
        }

        // Attendance Rate Calculation
        $attendanceRate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        // 6. Call Effectiveness Stats (Doughnut Chart Data)
        $callStats = CallLog::whereBetween('call_time', [$from->startOfDay(), $to->endOfDay()])
            ->select('result', DB::raw('count(*) as total'))
            ->groupBy('result')
            ->pluck('total', 'result')
            ->toArray();

        // 7. Daily Trend Chart Data
        $chartQuery = clone $baseQuery;
        $dailyCounts = $chartQuery->select(
            DB::raw("DATE($dateColumn) as day"),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('day')
        ->orderBy('day', 'asc')
        ->pluck('count', 'day')
        ->toArray();

        // Fill gaps in the chart
        $labels = [];
        $counts = [];
        $period = \Carbon\CarbonPeriod::create($from, $to);

        foreach ($period as $date) {
            $d = $date->toDateString();
            $labels[] = $d;
            $counts[] = $dailyCounts[$d] ?? 0;
        }

        return [
            'appts' => $appts,
            'kpis'  => [
                'total'      => $total,
                'present'    => $present,
                'notArrived' => $notArrived,
                'rate'       => $attendanceRate // Passed to view
            ],
            'chart'     => ['labels' => $labels, 'counts' => $counts],
            'callStats' => $callStats, // Passed to view
            'status'    => $status,
        ];
    }
}