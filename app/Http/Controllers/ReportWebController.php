<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Attendance;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportWebController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $fromStr = $today->toDateString();
        $toStr = $fromStr;
        $status = null;

        $from = $today->startOfDay();
        $to = $today->endOfDay();

        $data = ['from' => $fromStr, 'to' => $toStr, 'status' => $status];
        $report = $this->getReportData($from, $to, $status);

        // ensure comparison always exists (null when not comparing months)
        return view('reports.index', [
        'from' => $fromStr,
        'to' => $toStr,
        'status' => $status,
        'appts' => $report['appts'] ?? (new \Illuminate\Pagination\LengthAwarePaginator(collect([]), 0, 25)),
        'kpis' => $report['kpis'] ?? ['total' => 0, 'present' => 0, 'notArrived' => 0],
        'chart' => $report['chart'] ?? ['labels' => [], 'counts' => []],
        'comparison' => null,
    ]);

    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date',
            'status' => 'nullable|string|in:scheduled,present,missed,cancelled',
            'month1' => 'nullable|date',
            'month2' => 'nullable|date',
        ]);

        $from = Carbon::parse($data['from'])->startOfDay();
        $to   = Carbon::parse($data['to'])->endOfDay();

        if ($from->gt($to)) {
            return back()->withErrors(['from' => 'From date must be before or equal to To date'])->withInput();
        }

        $comparison = null;
        if ($request->filled('month1') && $request->filled('month2')) {
            $month1From = Carbon::parse($data['month1'])->startOfMonth();
            $month1To = $month1From->copy()->endOfMonth();
            $month2From = Carbon::parse($data['month2'])->startOfMonth();
            $month2To = $month2From->copy()->endOfMonth();

            $month1Data = $this->getReportData($month1From, $month1To, $data['status'] ?? null);
            $month2Data = $this->getReportData($month2From, $month2To, $data['status'] ?? null);

            $total1 = $month1Data['kpis']['total'];
            $total2 = $month2Data['kpis']['total'];
            $change = $total2 - $total1;
            $pctChange = $total1 > 0 ? round(($change / $total1) * 100) : 0;
            $direction = $change > 0 ? 'increased' : ($change < 0 ? 'decreased' : 'stable');

            $comparison = [
                'month1' => $data['month1'],
                'month2' => $data['month2'],
                'month1_data' => $month1Data['kpis'],
                'month2_data' => $month2Data['kpis'],
                'change_summary' => "Appointments {$direction} by {$pctChange}% (from {$total1} to {$total2}) between " . $month1From->format('F Y') . " and " . $month2From->format('F Y') . ".",
                'chart_type' => 'bar',
                'chart_labels' => [$month1From->format('F Y'), $month2From->format('F Y')],
                'chart_datasets' => [
                    [
                        'label' => $month1From->format('F Y'),
                        'data' => [$total1],
                        'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    ],
                    [
                        'label' => $month2From->format('F Y'),
                        'data' => [$total2],
                        'backgroundColor' => 'rgba(251, 191, 36, 0.5)',
                    ]
                ]
            ];

            // For table, use combined or just show summary; here, set appts to empty since comparison
            $appts = new LengthAwarePaginator(collect([]), 0, 25);
            $kpis = ['total' => $total1 + $total2, 'present' => $month1Data['kpis']['present'] + $month2Data['kpis']['present'], 'notArrived' => $month1Data['kpis']['notArrived'] + $month2Data['kpis']['notArrived']];
            $chart = ['labels' => [], 'counts' => []]; // Override with comparison chart
        } else {
            $report = $this->getReportData($from, $to, $data['status'] ?? null);
            $appts = $report['appts'];
            $kpis = $report['kpis'];
            $chart = $report['chart'];
            $comparison = null;
        }

        return view('reports.index', array_merge($data, compact('appts', 'kpis', 'chart', 'comparison')));
    }

    private function getReportData(Carbon $from, Carbon $to, ?string $status): array
    {
        $dateColumn = Schema::hasColumn('appointments', 'scheduled_date') ? 'scheduled_date' : (Schema::hasColumn('appointments', 'date') ? 'date' : null);

        if (!$dateColumn) {
            abort(500, 'No appointment date column found. Please ensure `scheduled_date` or `date` exists.');
        }

        $query = Appointment::with('patient')
            ->whereBetween($dateColumn, [$from->toDateString(), $to->toDateString()]);

        if (!empty($status)) {
            switch ($status) {
                case 'present':
                    $query->whereIn('status', ['queued', 'in_room', 'seen', 'present']);
                    break;
                case 'missed':
                    $query->where('status', 'missed');
                    break;
                case 'scheduled':
                    $query->where('status', 'scheduled');
                    break;
                case 'cancelled':
                    $query->where('status', 'cancelled');
                    break;
            }
        }

        // Paginate results
        $apptsQuery = (clone $query)->orderBy($dateColumn, 'asc');
        if (Schema::hasColumn('appointments', 'time')) {
            $apptsQuery->orderBy('time', 'asc');
        } else {
            $apptsQuery->orderBy('id', 'asc');
        }
        $appts = $apptsQuery->paginate(25)->appends(request()->query());

        // KPIs
        $total = (clone $query)->count();

        if (!empty($status)) {
            // Filtered: derive from status
            $present = ($status === 'present') ? $total : 0;
            $notArrived = ($status === 'missed') ? $total : 0;
        } else {
            // No filter: prefer Attendance if available
            $present = 0;
            if (Schema::hasTable('attendances')) {
                $present = Attendance::whereBetween('date', [$from->toDateString(), $to->toDateString()])
                    ->where('is_present', true)
                    ->count();
            }
            if ($present === 0) {
                $present = (clone $query)->whereIn('status', ['queued', 'in_room', 'seen', 'present'])->count();
            }
            $notArrived = max(0, $total - $present);
        }

        // Daily series for chart
        $seriesQuery = Appointment::select(
            DB::raw("DATE({$dateColumn}) as day"),
            DB::raw('COUNT(*) as count')
        )->whereBetween($dateColumn, [$from->toDateString(), $to->toDateString()]);

        if (!empty($status)) {
            switch ($status) {
                case 'present':
                    $seriesQuery->whereIn('status', ['queued', 'in_room', 'seen', 'present']);
                    break;
                case 'missed':
                    $seriesQuery->where('status', 'missed');
                    break;
                case 'scheduled':
                    $seriesQuery->where('status', 'scheduled');
                    break;
                case 'cancelled':
                    $seriesQuery->where('status', 'cancelled');
                    break;
            }
        }

        $daily = $seriesQuery->groupBy('day')->orderBy('day', 'asc')->get()->pluck('count', 'day')->toArray();

        // Fill gaps
        $labels = [];
        $counts = [];
        $cur = $from->copy();
        while ($cur->lte($to)) {
            $d = $cur->toDateString();
            $labels[] = $d;
            $counts[] = $daily[$d] ?? 0;
            $cur->addDay();
        }

        return [
            'appts' => $appts,
            'kpis' => ['total' => $total, 'present' => $present, 'notArrived' => $notArrived],
            'chart' => ['labels' => $labels, 'counts' => $counts],
            'status' => $status,
        ];
    }
}