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
use App\Exports\ClinicalReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportWebController extends Controller
{
    /**
     * Display the report dashboard (Defaults to This Month).
     */
    public function index(Request $request)
    {
        // Default: Start of this month to End of this month
        $from = $request->input('from') ? Carbon::parse($request->input('from')) : Carbon::now()->startOfMonth();
        $to   = $request->input('to') ? Carbon::parse($request->input('to')) : Carbon::now()->endOfMonth();
        $status = $request->input('status');

        return $this->generateReportResponse($from, $to, $status);
    }

    /**
     * Handle the "Generate" button submission.
     */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'from'   => 'required|date',
            'to'     => 'required|date',
            'status' => 'nullable|string|in:scheduled,present,missed,cancelled',
        ]);

        $from = Carbon::parse($data['from'])->startOfDay();
        $to   = Carbon::parse($data['to'])->endOfDay();

        if ($from->gt($to)) {
            return back()->withErrors(['from' => 'Start date cannot be after end date.'])->withInput();
        }

        return $this->generateReportResponse($from, $to, $data['status'] ?? null);
    }

    /**
     * Handle the Excel/CSV Export request.
     */
    public function export(Request $request)
    {
        $request->validate([
            'from'   => 'required|date',
            'to'     => 'required|date',
            'format' => 'required|in:csv,excel',
            'status' => 'nullable|string'
        ]);

        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();
        
        // 1. Fetch Data (No Pagination)
        $query = $this->buildBaseQuery($from, $to, $request->status);
        $appts = $query->with('patient')->orderBy('date', 'desc')->get();

        // 2. Calculate KPIs for the Excel Header
        // We reuse the logic manually here to avoid overhead of charts/pagination
        $total = $appts->count();
        $present = $appts->whereIn('status', ['queued', 'in_room', 'seen', 'present'])->count();
        $notArrived = $appts->whereIn('status', ['missed', 'absent'])->count();
        $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;
        
        $newVisits = $appts->filter(function($a) {
            return $a->patient && $a->patient->created_at->isSameDay($a->date);
        })->count();
        
        $referrals = Visit::whereBetween('created_at', [$from, $to])->whereNotNull('referral_to')->count();

        // 3. Prepare Data Structure
        $data = [
            'filters' => ['from' => $from, 'to' => $to],
            'appts'   => $appts,
            'kpis'    => [
                'total'      => $total,
                'present'    => $present,
                'rate'       => $rate,
                'new'        => $newVisits,
                'notArrived' => $notArrived,
                'referrals'  => $referrals
            ]
        ];

        // 4. Download
        $fileName = 'Clinic_Report_' . $from->format('d-M') . '_to_' . $to->format('d-M-Y');
        $ext = $request->format === 'excel' ? '.xlsx' : '.csv';
        $writerType = $request->format === 'excel' ? \Maatwebsite\Excel\Excel::XLSX : \Maatwebsite\Excel\Excel::CSV;

        return Excel::download(new ClinicalReportExport($data), $fileName . $ext, $writerType);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Helper: Builds the standard Eloquent query for appointments.
     */
    private function buildBaseQuery(Carbon $from, Carbon $to, ?string $status)
    {
        $dateColumn = Schema::hasColumn('appointments', 'scheduled_date') ? 'scheduled_date' : 'date';
        $query = Appointment::query()->whereBetween($dateColumn, [$from->toDateString(), $to->toDateString()]);

        if (!empty($status)) {
            if ($status === 'present') $query->whereIn('status', ['queued', 'in_room', 'seen', 'present']);
            elseif ($status === 'missed') $query->where('status', 'missed');
            elseif ($status === 'scheduled') $query->where('status', 'scheduled');
            elseif ($status === 'cancelled') $query->where('status', 'cancelled');
        }

        return $query;
    }

    /**
     * Helper: Generates the view response with all charts and metrics.
     */
    private function generateReportResponse(Carbon $from, Carbon $to, ?string $status)
    {
        $reportData = $this->calculateReportMetrics($from, $to, $status);

        return view('reports.index', [
            'from'       => $from->toDateString(),
            'to'         => $to->toDateString(),
            'status'     => $status,
            'appts'      => $reportData['appts'],
            'kpis'       => $reportData['kpis'],
            'chart'      => $reportData['chart'], 
            'chartTitle' => $reportData['chartTitle'],
            'callStats'  => $reportData['callStats'],
            'comparison' => null, // Comparison logic removed for brevity/focus on core
        ]);
    }

    /**
     * Core Calculation Logic
     */
    private function calculateReportMetrics(Carbon $from, Carbon $to, ?string $status): array
    {
        // 1. Base Query
        $baseQuery = $this->buildBaseQuery($from, $to, $status);
        $dateColumn = Schema::hasColumn('appointments', 'scheduled_date') ? 'scheduled_date' : 'date';

        // 2. Chart Logic (Daily vs Monthly)
        $diffInDays = $from->diffInDays($to);
        $isMonthly = $diffInDays > 90;
        
        $chartLabels = [];
        $chartCounts = [];
        $chartTitle = "Daily Appointments Trend";

        $chartQuery = clone $baseQuery;

        if ($isMonthly) {
            $chartTitle = "Monthly Appointments Trend";
            // Group by Month (YYYY-MM)
            $raw = $chartQuery->select(
                DB::raw("DATE_FORMAT($dateColumn, '%Y-%m') as dkey"),
                DB::raw('count(*) as total')
            )->groupBy('dkey')->pluck('total', 'dkey')->toArray();

            $current = $from->copy()->startOfMonth();
            while ($current->lte($to)) {
                $key = $current->format('Y-m');
                $chartLabels[] = $current->format('M Y'); // "Jan 2025"
                $chartCounts[] = $raw[$key] ?? 0;
                $current->addMonth();
            }
        } else {
            // Group by Day
            $raw = $chartQuery->select(
                DB::raw("DATE($dateColumn) as dkey"),
                DB::raw('count(*) as total')
            )->groupBy('dkey')->pluck('total', 'dkey')->toArray();

            $current = $from->copy();
            while ($current->lte($to)) {
                $key = $current->format('Y-m-d');
                $chartLabels[] = $current->format('d/m'); // "25/12"
                $chartCounts[] = $raw[$key] ?? 0;
                $current->addDay();
            }
        }

        // 3. Peak Hours Logic (Busiest Times)
        // Only relevant if we have 'time' column and not filtered by cancelled/missed usually
        $peakLabels = [];
        $peakCounts = [];
        if (Schema::hasColumn('appointments', 'time')) {
            $peakQuery = clone $baseQuery;
            $peakRaw = $peakQuery->whereNotNull('time')
                ->select(DB::raw('HOUR(time) as h'), DB::raw('count(*) as c'))
                ->groupBy('h')->pluck('c', 'h')->toArray();
            
            // Hours 8 to 17 (5 PM)
            for ($h = 8; $h <= 17; $h++) {
                $peakLabels[] = date('g A', mktime($h, 0));
                $peakCounts[] = $peakRaw[$h] ?? 0;
            }
        }

        // 4. Paginated List (Reduced to 10 per page for better UX)
        $listQuery = clone $baseQuery;
        $appts = $listQuery->with('patient')
            ->orderBy($dateColumn, 'desc')
            ->orderBy('time', 'desc') // Assuming 'time' column exists
            ->paginate(10)
            ->appends(request()->query());

        // 5. KPIs
        $kpiQuery = clone $baseQuery;
        $total = $kpiQuery->count();
        // Reset query for specific counts to avoid double filtering issues if needed, 
        // but 'clone' retains the date range which is what we want.
        $present = (clone $baseQuery)->whereIn('status', ['queued', 'in_room', 'seen', 'present'])->count();
        $notArrived = (clone $baseQuery)->whereIn('status', ['missed', 'absent'])->count();
        $cancelled = (clone $baseQuery)->where('status', 'cancelled')->count();
        $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        // Workload (New vs Review) - Requires fetching IDs to compare
        // We limit this calculation to avoid memory issues on huge datasets
        // If dataset > 10000, maybe skip this or use SQL join. For now, PHP logic:
        $newVisits = 0;
        if ($total < 5000) {
            $allIds = (clone $baseQuery)->with('patient:id,created_at')->get();
            $newVisits = $allIds->filter(function($a) {
                return $a->patient && $a->patient->created_at->isSameDay($a->date);
            })->count();
        }
        $reviews = max(0, $total - $newVisits);

        // Referrals
        $referrals = Visit::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->whereNotNull('referral_to')->count();

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
                'cancelled'  => $cancelled,
                'rate'       => $rate,
                'new'        => $newVisits,
                'review'     => $reviews,
                'referrals'  => $referrals
            ],
            'chart' => [
                'labels' => $chartLabels, 
                'counts' => $chartCounts,
                'peakChart' => ['labels' => $peakLabels, 'counts' => $peakCounts] // Add Peak Data
            ],
            'chartTitle' => $chartTitle,
            'callStats'  => $callStats,
        ];
    }
}