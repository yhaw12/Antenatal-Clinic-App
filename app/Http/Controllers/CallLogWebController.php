<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallLogWebController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', null);
        $period = $request->query('period', 'week');
        $pageSize = 40;

        $logsQuery = CallLog::with(['patient', 'caller'])->orderByDesc('call_time');
        if ($date) {
            $logsQuery->whereDate('call_time', $date);
        }

        $logs = $logsQuery->paginate($pageSize)->appends($request->query());

        $today = \Illuminate\Support\Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $today->copy()->endOfWeek();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        if ($date) {
            try {
                $pivot = \Illuminate\Support\Carbon::parse($date);
                $weekStart = $pivot->copy()->startOfWeek();
                $weekEnd = $pivot->copy()->endOfWeek();
                $monthStart = $pivot->copy()->startOfMonth();
                $monthEnd = $pivot->copy()->endOfMonth();
            } catch (\Throwable $e) {
            }
        }

        $callsWeekCount = CallLog::whereBetween('call_time', [$weekStart->startOfDay(), $weekEnd->endOfDay()])->count();
        $callsMonthCount = CallLog::whereBetween('call_time', [$monthStart->startOfDay(), $monthEnd->endOfDay()])->count();

        $apptsWeekCount = Appointment::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])->count();
        $apptsMonthCount = Appointment::whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])->count();

        $notCalledWeekCount = max(0, $apptsWeekCount - $callsWeekCount);
        $notCalledMonthCount = max(0, $apptsMonthCount - $callsMonthCount);

        $calledAppointmentIdsWeek = CallLog::whereBetween('call_time', [$weekStart->startOfDay(), $weekEnd->endOfDay()])
            ->whereNotNull('appointment_id')->pluck('appointment_id')->unique()->toArray();

        $calledAppointmentIdsMonth = CallLog::whereBetween('call_time', [$monthStart->startOfDay(), $monthEnd->endOfDay()])
            ->whereNotNull('appointment_id')->pluck('appointment_id')->unique()->toArray();

        $notCalledAppointmentsWeek = Appointment::with('patient')
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereNotIn('id', $calledAppointmentIdsWeek)
            ->orderBy('date')->orderBy('time')
            ->limit(200)->get();

        $notCalledAppointmentsMonth = Appointment::with('patient')
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->whereNotIn('id', $calledAppointmentIdsMonth)
            ->orderBy('date')->orderBy('time')
            ->limit(200)->get();

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
            'patient_id'     => 'required|exists:patients,id', // This was failing because input was missing in View
            'result'         => 'required|in:no_answer,rescheduled,will_attend,refused,incorrect_number',
            'notes'          => 'nullable|string',
        ]);

        try {
            $data['called_by'] = optional($request->user())->id;
            $data['call_time'] = now();

            $call = CallLog::create($data);

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

            // CRITICAL FIX: Return JSON if AJAX
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Call logged successfully',
                    'data' => $call
                ]);
            }

            return redirect()->route('call_logs')->with('success', 'Call logged');
        } catch (\Exception $e) {
            Log::error('Failed to log call', ['error' => $e->getMessage(), 'payload' => $data]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Could not log call: ' . $e->getMessage()], 500);
            }
            
            return back()->with('error', 'Could not log call')->withInput();
        }
    }

    // CRITICAL FIX: Added missing method for "Mark Seen"
    public function markSeen(Request $request, $id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->update(['status' => 'seen']);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Appointment marked as seen']);
            }

            return back()->with('success', 'Appointment marked as seen');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Failed to update status'], 500);
            }
            return back()->with('error', 'Failed to update status');
        }
    }
}