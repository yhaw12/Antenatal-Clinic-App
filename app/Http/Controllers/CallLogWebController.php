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
        $date = $request->query('date', null);

        $query = CallLog::with('patient')->orderByDesc('call_time');
        if ($date) {
            $query->whereDate('call_time', $date);
        }

        $logs = $query->paginate(40);
        return view('call_logs.index', compact('logs'));
    }

    public function create(Request $request)
    {
        $appointmentId = $request->query('appointment_id');
        $patientId = $request->query('patient_id');

        $appointment = $appointmentId ? Appointment::with('patient')->find($appointmentId) : null;
        $patient = $patientId ? Patient::find($patientId) : null;

        return view('call_logs.new', compact('appointment', 'patient'));
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
