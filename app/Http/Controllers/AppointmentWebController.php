<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AppointmentWebController extends Controller
{
    /**
     * List appointments (most recent first).
     * Supports optional filtering:
     *  - ?date=YYYY-MM-DD
     *  - ?status=scheduled|queued|cancelled|completed
     *  - ?q=search (patient name / hospital_number)
     *  - ?per_page= (pagination)
     */
    public function index(Request $request)
    {
        $date = $request->query('date');
        $status = $request->query('status');
        $q = trim($request->query('q', ''));
        $perPage = (int) $request->query('per_page', 25);
        $perPage = $perPage > 0 && $perPage <= 200 ? $perPage : 25;

        $query = Appointment::with('patient')->orderByDesc('date')->orderByDesc('time');

        if ($date) {
            $query->whereDate('date', $date);
        }

        if ($status && in_array($status, ['scheduled','queued','cancelled','completed'], true)) {
            $query->where('status', $status);
        }

        if ($q !== '') {
            // search patient first_name, last_name or hospital_number
            $query->whereHas('patient', function ($pq) use ($q) {
                $pq->where(function ($w) use ($q) {
                    $w->where('first_name', 'like', "%{$q}%")
                      ->orWhere('last_name', 'like', "%{$q}%")
                      ->orWhere('hospital_number', 'like', "%{$q}%");
                });
            });
        }

        $appointments = $query->paginate($perPage)->appends($request->query());

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

        // Normalize: ensure time is either null or HH:MM
        if (empty($data['time'])) {
            $data['time'] = null;
        }

        try {
            $appointment = DB::transaction(function () use ($data) {
                return Appointment::create([
                    'patient_id' => $data['patient_id'],
                    'date' => $data['date'],
                    'time' => $data['time'],
                    'status' => $data['status'] ?? 'scheduled',
                    'notes' => $data['notes'] ?? null,
                ]);
            });

            if ($request->ajax() || $request->wantsJson()) {
                // Eager-load patient for response
                $appointment->load('patient');
                return response()->json(['success' => true, 'appointment' => $appointment], 201);
            }

            return redirect()->route('appointments.index')->with('success', 'Appointment created');
        } catch (\Exception $e) {
            Log::error('Failed to create appointment', [
                'error' => $e->getMessage(),
                'payload' => $data,
                'trace' => $e->getTraceAsString(),
            ]);

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

        // Remove nulls so we only update provided fields (but allow empty string for notes)
        $updatePayload = array_filter($data, function ($v) {
            return $v !== null;
        });

        // If user intentionally sent empty notes string, preserve it
        if (array_key_exists('notes', $data) && $data['notes'] === '') {
            $updatePayload['notes'] = '';
        }

        try {
            DB::transaction(function () use ($appointment, $updatePayload) {
                if (!empty($updatePayload)) {
                    $appointment->update($updatePayload);
                }
            });

            if ($request->ajax() || $request->wantsJson()) {
                $appointment->refresh()->load('patient');
                return response()->json(['success' => true, 'appointment' => $appointment]);
            }

            return redirect()->route('appointments.index')->with('success', 'Appointment updated');
        } catch (\Exception $e) {
            Log::error('Failed to update appointment', [
                'id' => $appointment->id,
                'error' => $e->getMessage(),
                'payload' => $updatePayload,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Could not update appointment'], 500);
            }

            return back()->with('error', 'Could not update appointment')->withInput();
        }
    }
}
