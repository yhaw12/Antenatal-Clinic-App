<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DailyQueueController extends Controller
{
    /**
     * Mark a single appointment (by appointment_id) as present.
     * Accepts { appointment_id } or { appointment_ids: [..] } for bulk.
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
            foreach ($ids as $id) {
                $appt = Appointment::find($id);
                if (! $appt) continue;

                // mark attendance record for the patient on that date
                Attendance::updateOrCreate(
                    ['patient_id' => $appt->patient_id, 'date' => $date],
                    ['is_present' => true]
                );

                // update appointment status (queued)
                $appt->update(['status' => 'queued']);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('markPresent failed', ['error' => $e->getMessage(), 'ids' => $ids]);
            return response()->json(['message' => 'Failed to mark present'], 500);
        }
    }

    /**
     * Mark a single appointment (or many) as absent.
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
            foreach ($ids as $id) {
                $appt = Appointment::find($id);
                if (! $appt) continue;

                // mark attendance explicitly as absent (if you want a record)
                Attendance::updateOrCreate(
                    ['patient_id' => $appt->patient_id, 'date' => $date],
                    ['is_present' => false]
                );

                // update appointment status
                $appt->update(['status' => 'absent']);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('markAbsent failed', ['error' => $e->getMessage(), 'ids' => $ids]);
            return response()->json(['message' => 'Failed to mark absent'], 500);
        }
    }
}
