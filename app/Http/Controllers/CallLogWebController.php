<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallLogWebController extends Controller
{
    public function index(Request $request)
    {
        // incoming filters
        $date = $request->query('date', null);
        $period = $request->query('period', 'week'); // 'week' | 'month'
        $pageSize = 40;

        // Base logs query (apply optional date filter for single-day view)
        $logsQuery = CallLog::with('patient')->orderByDesc('call_time');
        if ($date) {
            $logsQuery->whereDate('call_time', $date);
        }

        $logs = $logsQuery->paginate($pageSize)->appends($request->query());

        // Determine week and month ranges (ISO week: monday->sunday)
        $today = \Illuminate\Support\Carbon::today();
        $weekStart = $today->copy()->startOfWeek(); // monday
        $weekEnd = $today->copy()->endOfWeek();     // sunday

        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        // If user passed a date, pivot ranges around that date
        if ($date) {
            try {
                $pivot = \Illuminate\Support\Carbon::parse($date);
                $weekStart = $pivot->copy()->startOfWeek();
                $weekEnd = $pivot->copy()->endOfWeek();
                $monthStart = $pivot->copy()->startOfMonth();
                $monthEnd = $pivot->copy()->endOfMonth();
            } catch (\Throwable $e) {
                // ignore parse errors and continue with today
            }
        }

        // Calls made counts for ranges
        $callsWeekCount = CallLog::whereBetween('call_time', [$weekStart->startOfDay(), $weekEnd->endOfDay()])->count();
        $callsMonthCount = CallLog::whereBetween('call_time', [$monthStart->startOfDay(), $monthEnd->endOfDay()])->count();

        // Expected calls = number of appointments scheduled in the range.
        // (If your expected definition differs, change query accordingly.)
        $apptsWeekCount = Appointment::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])->count();
        $apptsMonthCount = Appointment::whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])->count();

        // Not-called = appointments count minus calls made (floor at 0)
        $notCalledWeekCount = max(0, $apptsWeekCount - $callsWeekCount);
        $notCalledMonthCount = max(0, $apptsMonthCount - $callsMonthCount);

        // Build lists of appointments that have no call recorded in the range.
        // We assume CallLog may reference an appointment via appointment_id (nullable). If your schema differs adjust.
        $calledAppointmentIdsWeek = CallLog::whereBetween('call_time', [$weekStart->startOfDay(), $weekEnd->endOfDay()])
            ->whereNotNull('appointment_id')->pluck('appointment_id')->unique()->toArray();

        $calledAppointmentIdsMonth = CallLog::whereBetween('call_time', [$monthStart->startOfDay(), $monthEnd->endOfDay()])
            ->whereNotNull('appointment_id')->pluck('appointment_id')->unique()->toArray();

        $notCalledAppointmentsWeek = Appointment::with('patient')
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereNotIn('id', $calledAppointmentIdsWeek)
            ->orderBy('date')->orderBy('time')
            ->limit(200)
            ->get();

        $notCalledAppointmentsMonth = Appointment::with('patient')
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->whereNotIn('id', $calledAppointmentIdsMonth)
            ->orderBy('date')->orderBy('time')
            ->limit(200)
            ->get();

        return view('call_logs.index', [
            'logs' => $logs,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'callsWeekCount' => $callsWeekCount,
            'callsMonthCount' => $callsMonthCount,
            'apptsWeekCount' => $apptsWeekCount,
            'apptsMonthCount' => $apptsMonthCount,
            'notCalledWeekCount' => $notCalledWeekCount,
            'notCalledMonthCount' => $notCalledMonthCount,
            'notCalledAppointmentsWeek' => $notCalledAppointmentsWeek,
            'notCalledAppointmentsMonth' => $notCalledAppointmentsMonth,
            'selectedPeriod' => $period,
            'filterDate' => $date,
        ]);
    }


    public function create(Request $request)
    {
        $appointmentId = $request->query('appointment_id');
        $patientId = $request->query('patient_id');

        $appointment = $appointmentId ? Appointment::with('patient')->find($appointmentId) : null;
        $patient = $patientId ? Patient::find($patientId) : null;

        return view('call_logs.create', compact('appointment', 'patient'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'appointment_id' => 'nullable|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
            'result' => 'required|in:no_answer,rescheduled,will_attend,refused,incorrect_number',
            'notes' => 'nullable|string',
        ]);

        try {
            $data['called_by'] = optional($request->user())->id;
            $data['call_time'] = now();

            $call = CallLog::create($data);

            // Update appointment status if we logged against an appointment
            if (!empty($data['appointment_id'])) {
                $appt = Appointment::find($data['appointment_id']);
                if ($appt) {
                    if ($data['result'] === 'rescheduled') {
                        $appt->update(['status' => 'scheduled']);
                    } elseif ($data['result'] === 'will_attend') {
                        $appt->update(['status' => 'queued']);
                    }
                }
            }

            return redirect()->route('call-logs')->with('success', 'Call logged');
        } catch (\Exception $e) {
            Log::error('Failed to log call', ['error' => $e->getMessage(), 'payload' => $data]);
            return back()->with('error', 'Could not log call')->withInput();
        }
    }
}
