<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AppointmentWebController extends Controller
{
    /**
     * List appointments (most recent first)
     */
    public function index(Request $request)
    {
        $appointments = Appointment::with('patient')->orderByDesc('date')->orderByDesc('time')->paginate(25);
        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show form to create an appointment
     */
    public function create()
    {
        // encrypted patient names cannot be reliably ordered/searched in DB.
        // Show most recently created patients as a pragmatic default.
        $patients = Patient::orderByDesc('created_at')->limit(200)->get();
        return view('appointments.create', compact('patients'));
    }

    /**
     * Store a new appointment (for a given patient)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'status' => ['nullable', Rule::in(['scheduled','queued','cancelled','completed'])],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $appointment = Appointment::create([
                'patient_id' => $data['patient_id'],
                'date' => $data['date'],
                'time' => $data['time'] ?? null,
                'status' => $data['status'] ?? 'scheduled',
                'notes' => $data['notes'] ?? null,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'appointment' => $appointment], 201);
            }

            return redirect()->route('appointments.index')->with('success', 'Appointment created');
        } catch (\Exception $e) {
            Log::error('Failed to create appointment', ['error' => $e->getMessage(), 'payload' => $data]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Could not create appointment'], 500);
            }
            return back()->with('error', 'Could not create appointment')->withInput();
        }
    }

    /**
     * Update appointment (partial updates allowed)
     */
    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'date' => ['nullable', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'status' => ['nullable', Rule::in(['scheduled','queued','cancelled','completed'])],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $appointment->update(array_filter($data, fn($v) => $v !== null));
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'appointment' => $appointment]);
            }
            return redirect()->route('appointments.index')->with('success', 'Appointment updated');
        } catch (\Exception $e) {
            Log::error('Failed to update appointment', ['id' => $appointment->id, 'error' => $e->getMessage()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Could not update appointment'], 500);
            }
            return back()->with('error', 'Could not update appointment')->withInput();
        }
    }
}
