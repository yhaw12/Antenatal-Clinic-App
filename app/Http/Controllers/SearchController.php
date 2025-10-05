<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    /**
     * AJAX search used for the dashboard suggestions box.
     * Query params:
     *  - term (string) required (min 1 char)
     *  - date (optional) to prefer appointments on that date
     */
    public function patients(Request $request)
{
    $term = trim((string) $request->query('term', ''));
    if (mb_strlen($term) < 1) {
        return response()->json([], 200);
    }

    $date = $request->query('date', null);

    // normalize search term for name_search
    $termNormalized = mb_strtolower(preg_replace('/\s+/', ' ', $term));
    $digits = preg_replace('/\D+/', '', $term);

    try {
        $query = Patient::query();

        if ($date) {
            $query->whereHas('appointments', function ($q) use ($date) {
                $q->whereDate('date', $date);
            });
        }

        $query->with(['appointments' => function ($q) use ($date) {
            if ($date) $q->whereDate('date', $date)->orderBy('time');
            else $q->orderByDesc('date')->orderByDesc('time');
            $q->limit(1);
        }]);

        $query->where(function ($q) use ($termNormalized, $digits, $term) {
            $q->where('name_search', 'like', "%{$termNormalized}%");
            if ($digits !== '') {
                $q->orWhere('phone_search', 'like', "%{$digits}%");
            }
            $q->orWhere('hospital_number', 'like', "%{$term}%")
              ->orWhere('id_number', 'like', "%{$term}%");
        });

        $patients = $query->limit(12)->get();

        $results = $patients->map(function ($p) {
            // try to decrypt for display; if decrypt fails, fallback to name_search prettified
            $first = null; $last = null; $phone = null;
            try { $first = $p->first_name; } catch (\Throwable $e) { /* ignore */ }
            try { $last  = $p->last_name;  } catch (\Throwable $e) { /* ignore */ }
            try { $phone = $p->phone;      } catch (\Throwable $e) { /* ignore */ }

            if (empty($first) && empty($last) && !empty($p->name_search)) {
                $parts = preg_split('/\s+/', $p->name_search);
                $parts = array_map(function ($n) {
                    return mb_convert_case($n, MB_CASE_TITLE, "UTF-8");
                }, $parts);
                $first = $parts[0] ?? null;
                $last  = count($parts) > 1 ? $parts[count($parts)-1] : null;
            }

            $appt = optional($p->appointments->first());
            $apptTime = null;
            $apptDate = null;
            if ($appt && $appt->time) {
                try { $apptTime = \Illuminate\Support\Carbon::parse($appt->time)->format('h:i A'); } catch (\Throwable $e) { $apptTime = $appt->time; }
            }
            if ($appt && $appt->date) {
                try { $apptDate = \Illuminate\Support\Carbon::parse($appt->date)->toDateString(); } catch (\Throwable $e) { $apptDate = $appt->date; }
            }

            $fullName = trim(($first ?? '') . ' ' . ($last ?? ''));
            if (!$fullName) {
                $fullName = $p->name_search ? mb_convert_case($p->name_search, MB_CASE_TITLE, "UTF-8") : 'Unknown';
            }

            return [
                'id' => $p->id,
                'patient_id' => $p->id,
                'label' => $fullName,
                'first_name' => $first,
                'last_name' => $last,
                'initials' => strtoupper(mb_substr((string)($first ?? ''),0,1) . mb_substr((string)($last ?? ''),0,1)),
                'phone' => $phone,
                'hospital_number' => $p->hospital_number,
                'appointment_id' => $appt->id ?? null,
                'appointment_date' => $apptDate,
                'appointment_time' => $apptTime,
            ];
        })->values();

        return response()->json($results, 200);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Patient search error', [
            'term' => $term,
            'date' => $date,
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json([], 200);
    }
}

}