<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PatientWebController extends Controller
{
    public function index()
    {
        $patients = Patient::latest()->paginate(20);
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'folder_no' => 'nullable|string|max:191',
            'phone' => ['nullable','regex:/^0[0-9]{6,14}$/'],
            'whatsapp' => ['nullable','regex:/^0[0-9]{6,14}$/'],
            'room' => 'nullable|string|max:50',
            'next_of_kin_name' => 'nullable|string|max:191',
            'next_of_kin_phone' => ['nullable','regex:/^0[0-9]{6,14}$/'],
            'next_review_date' => 'nullable|date',
            'address' => 'nullable|string',
            'complaints' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $patient = Patient::create($request->only([
                'first_name','last_name','folder_no','phone','whatsapp','room',
                'next_of_kin_name','next_of_kin_phone','id_number','hospital_number',
                'next_review_date','address','complaints'
            ]));

            // create an appointment for next_review_date (or today)
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'date' => $request->input('next_review_date', Carbon::today()->toDateString()),
                'time' => $request->input('appointment_time', null),
                'status' => 'scheduled',
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'patient' => $patient,
                    'appointment' => $appointment,
                    'redirect' => route('patients.show', $patient->id)
                ]);
            }

            return redirect()->route('patients.index')->with('success', 'Patient registered');
        } catch (\Exception $e) {
            Log::error('Failed to create patient', ['error' => $e->getMessage()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Could not create patient'], 500);
            }
            return back()->with('error', 'Could not create patient')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $patient = Patient::with(['appointments' => function ($q) {
                $q->orderByDesc('date')->orderByDesc('time');
            }])->findOrFail($id);

            return view('patients.show', compact('patient'));
        } catch (\Exception $e) {
            Log::error('Failed to load patient', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('patients.index')->with('error', 'Failed to load patient');
        }
    }

    public function edit($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            return view('patients.edit', compact('patient'));
        } catch (\Exception $e) {
            Log::error('Failed to load patient for editing', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('patients.index')->with('error', 'Failed to load patient');
        }
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'phone' => ['nullable','regex:/^0[0-9]{6,14}$/'],
            'whatsapp' => ['nullable','regex:/^0[0-9]{6,14}$/'],
            'address' => 'nullable|string|max:500',
            'next_of_kin_name' => 'nullable|string|max:191',
            'next_of_kin_phone' => ['nullable','regex:/^0[0-9]{6,14}$/'],
            'room' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $patient->update($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'patient' => $patient]);
            }

            return redirect()->route('patients.show', $patient->id)->with('success', 'Patient updated');
        } catch (\Exception $e) {
            Log::error('Failed to update patient', ['id' => $patient->id, 'error' => $e->getMessage()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Failed to update patient'], 500);
            }
            return back()->with('error', 'Failed to update patient')->withInput();
        }
    }

    /**
     * Search patients for today (returns JSON)
     *
     * NOTE: encrypted fields (first_name, phone, folder_no, etc.) cannot be reliably searched
     * with SQL LIKE when using Laravel encrypted casts. This search uses
     * id_number, hospital_number and address which are not encrypted in your schema.
     */
    public function search(Request $request)
    {
        $term = trim($request->input('term', ''));
        $date = $request->input('date', now()->toDateString());

        $patients = Patient::where(function ($q) use ($term) {
                if ($term === '') {
                    $q->whereRaw('1 = 1');
                } else {
                    // search fields that are safe to query
                    $q->where('id_number', 'like', "%{$term}%")
                      ->orWhere('hospital_number', 'like', "%{$term}%")
                      ->orWhere('address', 'like', "%{$term}%");
                }
            })
            ->whereHas('appointments', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->with(['appointments' => function($q) use ($date) {
                $q->whereDate('date', $date)->orderByDesc('time');
            }])
            ->get()
            ->map(function ($patient) {
                $appt = optional($patient->appointments->first());
                return [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'address' => $patient->address,
                    'phone' => $patient->phone,
                    'next_of_kin_name' => $patient->next_of_kin_name,
                    'scheduled_date' => $appt->date ?? null,
                    'scheduled_time' => $appt->time ?? null,
                    'status' => $appt->status ?? 'scheduled',
                ];
            });

        return response()->json($patients);
    }
}
