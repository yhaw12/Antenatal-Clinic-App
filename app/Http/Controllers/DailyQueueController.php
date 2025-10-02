<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyQueueController extends Controller
{
    /**
     * Show daily appointments (unmarked first, marked to bottom).
     */
    public function index(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        // Put unmarked (not present/missed/absent/etc.) at the top,
        // and marked ones at the bottom. Adjust CASE list if you add other statuses.
        $daily = Appointment::with('patient')
            ->whereDate('date', $date)
            ->orderByRaw("
                CASE
                    WHEN status IN ('present','missed','absent','queued','in_room','seen','cancelled') THEN 1
                    ELSE 0
                END ASC
            ")
            ->orderBy('time')
            ->get();

        return view('daily_queue', compact('daily'));
    }

    /**
     * Mark one or many appointments as present.
     * Accepts { appointment_id } or { appointment_ids: [...] }.
     */
    public function markPresent(Request $request)
    {
        $payload = $request->validate([
            'appointment_id' => 'nullable|integer|exists:appointments,id',
            'appointment_ids' => 'nullable|array',
            'appointment_ids.*' => 'integer|exists:appointments,id',
        ]);

        $ids = $payload['appointment_ids'] ?? ($payload['appointment_id'] ? [$payload['appointment_id']] : []);
        if (empty($ids)) {
            return response()->json(['message' => 'No appointment ids provided'], 422);
        }

        $date = Carbon::today()->toDateString();

        try {
            DB::transaction(function () use ($ids, $date) {
                foreach ($ids as $id) {
                    $appt = Appointment::find($id);
                    if (! $appt) continue;

                    Attendance::updateOrCreate(
                        ['patient_id' => $appt->patient_id, 'date' => $date],
                        ['is_present' => true]
                    );

                    // Use direct assignment + save() to avoid fillable blocking
                    $appt->status = 'present';
                    $appt->save();
                }
            });

            $total = Appointment::whereDate('date', $date)->count();

            $present = Attendance::whereDate('date', $date)->where('is_present', true)->count();
            if ($present === 0) {
                $present = Appointment::whereDate('date', $date)
                    ->whereIn('status', ['queued', 'in_room', 'seen', 'present'])
                    ->count();
            }

            $notArrived = max(0, $total - $present);

            $next = Appointment::with('patient')
                ->whereDate('date', $date)
                ->whereNotIn('status', ['queued','in_room','seen','missed','absent','cancelled','present'])
                ->orderBy('time')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'total' => $total,
                'present' => $present,
                'notArrived' => $notArrived,
                'appointments' => $next,
            ], 200);
        } catch (\Exception $e) {
            Log::error('markPresent failed', ['error' => $e->getMessage(), 'ids' => $ids]);
            return response()->json(['message' => 'Failed to mark present'], 500);
        }
    }

    /**
     * Mark one or many appointments as absent/missed.
     * Accepts { appointment_id } or { appointment_ids: [...] }.
     */
    public function markAbsent(Request $request)
    {
        $payload = $request->validate([
            'appointment_id' => 'nullable|integer|exists:appointments,id',
            'appointment_ids' => 'nullable|array',
            'appointment_ids.*' => 'integer|exists:appointments,id',
        ]);

        $ids = $payload['appointment_ids'] ?? ($payload['appointment_id'] ? [$payload['appointment_id']] : []);
        if (empty($ids)) {
            return response()->json(['message' => 'No appointment ids provided'], 422);
        }

        $date = Carbon::today()->toDateString();

        try {
            DB::transaction(function () use ($ids, $date) {
                foreach ($ids as $id) {
                    $appt = Appointment::find($id);
                    if (! $appt) continue;

                    Attendance::updateOrCreate(
                        ['patient_id' => $appt->patient_id, 'date' => $date],
                        ['is_present' => false]
                    );

                    // unify on 'missed' so Blade shows red badge
                    $appt->status = 'missed';
                    $appt->save();
                }
            });

            $total = Appointment::whereDate('date', $date)->count();

            $present = Attendance::whereDate('date', $date)->where('is_present', true)->count();
            if ($present === 0) {
                $present = Appointment::whereDate('date', $date)
                    ->whereIn('status', ['queued', 'in_room', 'seen', 'present'])
                    ->count();
            }

            $notArrived = max(0, $total - $present);

            $next = Appointment::with('patient')
                ->whereDate('date', $date)
                ->whereNotIn('status', ['queued','in_room','seen','missed','absent','cancelled','present'])
                ->orderBy('time')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'total' => $total,
                'present' => $present,
                'notArrived' => $notArrived,
                'appointments' => $next,
            ], 200);
        } catch (\Exception $e) {
            Log::error('markAbsent failed', ['error' => $e->getMessage(), 'ids' => $ids]);
            return response()->json(['message' => 'Failed to mark absent'], 500);
        }
    }
}
