<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CallLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleApiController extends Controller
{
    // GET /api/schedule?date=YYYY-MM-DD&doctor=&clinic=&view=day
    public function list(Request $request)
    {
        $this->authorize('viewAny', Appointment::class); // policy/gate

        $date = $request->query('date', now()->toDateString());
        $doctor = $request->query('doctor');
        $clinic = $request->query('clinic');

        $query = Appointment::with(['patient','visit','createdBy'])
            ->where('scheduled_date', $date)
            ->orderBy('scheduled_time');

        if ($doctor) $query->where('doctor_id', $doctor);
        if ($clinic) $query->where('clinic_id', $clinic);

        $appointments = $query->get();

        // normalize payload for frontend
        $payload = $appointments->map(function($a){
            return [
                'id' => $a->id,
                'time' => $a->scheduled_time,
                'patient' => [
                    'id' => $a->patient->id,
                    'name' => $a->patient->first_name . ' ' . ($a->patient->last_name ?? ''),
                    'phone' => $a->patient->phone,
                ],
                'status' => $a->status,
                'visit' => $a->visit ? [
                    'id' => $a->visit->id,
                    'bp' => $a->visit->bp ?? null,
                    'complaints' => $a->visit->complaints ?? null,
                    'referral_to' => $a->visit->referral_to ?? null,
                    'chns_feedback' => $a->visit->chns_feedback ?? null,
                ] : null,
                'procedure_list' => $a->procedures ?? [], // if you store procedures JSON
                'billing_status' => $a->billing_status ?? null, // example fields
                'notes' => $a->notes ?? null
            ];
        });

        return response()->json(['date'=>$date, 'appointments'=>$payload]);
    }

    public function markArrived(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $appointment->status = 'queued';
        $appointment->save();

        // create or update a visit record
        $visit = $appointment->visit ?? $appointment->visit()->create([
            'patient_id' => $appointment->patient_id,
            'user_id' => $request->user()->id,
            'arrived_at' => now()
        ]);
        $visit->arrived_at = now();
        $visit->save();

        // audit log (optional)
        // activity()->causedBy($request->user())
        //     ->performedOn($appointment)
        //     ->withProperties(['action'=>'markArrived'])
        //     ->log('Appointment marked arrived');

        return response()->json(['ok'=>true,'status'=>'queued']);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);
        $data = $request->validate(['status'=>'required|string']);
        $appointment->status = $data['status'];
        $appointment->save();

        return response()->json(['ok'=>true,'status'=>$appointment->status]);
    }

    public function logCall(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);
        $data = $request->validate([
            'result' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        CallLog::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'called_by' => $request->user()->id,
            'call_time' => now(),
            'result' => $data['result'],
            'notes' => $data['notes'] ?? null
        ]);

        return response()->json(['ok'=>true]);
    }
}
