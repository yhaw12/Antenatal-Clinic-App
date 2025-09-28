<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * AJAX search used for the dashboard suggestions box.
     * Query params:
     *  - term (string) required (min 2 chars)
     *  - date (optional) to prefer today's appointments
     */
    public function patients(Request $request)
    {
        $term = trim($request->query('term', ''));
        if (mb_strlen($term) < 2) {
            return response()->json([], 200);
        }

        $termNormalized = mb_strtolower($term);

        $results = Patient::with(['appointments' => function($q) {
                $q->orderByDesc('date')->orderByDesc('time')->limit(1);
            }])
            ->where(function($q) use ($termNormalized) {
                // search the helper columns + hospital_number (not encrypted) for matches
                $q->where('name_search', 'like', "%{$termNormalized}%")
                  ->orWhere('phone_search', 'like', "%".preg_replace('/\D+/', '', $termNormalized)."%")
                  ->orWhere('hospital_number', 'like', "%{$termNormalized}%")
                  ->orWhere('id_number', 'like', "%{$termNormalized}%");
            })
            ->limit(12)
            ->get()
            ->map(function ($p) use ($term) {
                $appt = optional($p->appointments->first());
                $fullName = trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? ''));
                return [
                    'patient_id' => $p->id,
                    'label' => $fullName ?: 'Unknown',
                    'first_name' => $p->first_name,
                    'last_name' => $p->last_name,
                    'initials' => strtoupper(substr($p->first_name ?? '', 0, 1) . substr($p->last_name ?? '', 0, 1)),
                    'phone' => $p->phone,
                    'hospital_number' => $p->hospital_number,
                    'appointment_id' => $appt->id ?? null,
                    'appointment_date' => $appt->date ?? null,
                    'appointment_time' => $appt->time ?? null,
                ];
            });

        return response()->json($results);
    }
}
