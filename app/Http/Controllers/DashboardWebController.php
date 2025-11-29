<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Attendance;
use App\Models\CallLog;
use App\Models\Patient;
use App\Models\UserActivityLog;
use App\Services\ActivityLogger;
use Carbon\Carbon;

class DashboardWebController extends Controller
{
   public function stats(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $status = $request->query('status', '');

        $query = Appointment::with('patient')->whereDate('date', $date);

        if ($status) {
            switch ($status) {
                case 'present':
                    $query->whereIn('status', ['queued', 'in_room', 'seen', 'present']);
                    break;
                case 'missed':
                    $query->where('status', 'missed');
                    break;
                case 'scheduled':
                    $query->where('status', 'scheduled');
                    break;
            }
        }

        // Get collection to perform calculations
        $appointments = $query->get();
        $total = $appointments->count();

        // 1. KPI: Present / Missed
        $present = $appointments->whereIn('status', ['queued', 'in_room', 'seen', 'present'])->count();
        $notArrived = max(0, $total - $present);

        // 2. KPI: New vs Review
        // "New" = Patient registered on the same day as the appointment
        $newVisits = $appointments->filter(function($appt) use ($date) {
            return $appt->patient && $appt->patient->created_at->isSameDay(Carbon::parse($date));
        })->count();

        $reviews = max(0, $total - $newVisits);

        return response()->json([
            'total'      => $total, 
            'present'    => $present, 
            'notArrived' => $notArrived,
            'newVisits'  => $newVisits,
            'reviews'    => $reviews
        ]);
    }

    /**
     * Main Dashboard View
     */
    public function dashboard(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        // 1. Fetch Appointments (All statuses, let JS filter the view)
        $appointmentsQuery = Appointment::with('patient')
            ->whereDate('date', $date)
            ->orderBy('time');

        $appointments = $appointmentsQuery->paginate(20);

        // 2. Calculate KPIs for Initial Load
        $allAppts = Appointment::with('patient')->whereDate('date', $date)->get();
        $total = $allAppts->count();
        
        // Present logic (Fallback to Appointment status if Attendance table empty)
        $present = Attendance::whereDate('date', $date)->where('is_present', true)->count();
        if ($present === 0) {
            $present = $allAppts->whereIn('status', ['queued', 'in_room', 'seen', 'present'])->count();
        }
        $notArrived = max(0, $total - $present);

        // New vs Review Logic
        $newVisits = $allAppts->filter(function($appt) use ($date) {
            return $appt->patient && $appt->patient->created_at->isSameDay(Carbon::parse($date));
        })->count();
        $reviews = max(0, $total - $newVisits);

        // 3. Percentage Change (Visual Candy)
        $yesterday = Carbon::parse($date)->subDay()->toDateString();
        $yesterdayTotal = (int) Appointment::whereDate('date', $yesterday)->count();

        if ($yesterdayTotal === 0) {
            $percentageChange = $total > 0 ? 100 : 0;
            $changeDirection = $total > 0 ? '+' : '';
        } else {
            $diff = $total - $yesterdayTotal;
            $percentageChange = (int) round(($diff / $yesterdayTotal) * 100);
            $changeDirection = $percentageChange > 0 ? '+' : '-';
            $percentageChange = abs($percentageChange);
        }

        // 4. Sidebar Data
        $callList = CallLog::with('patient')
            ->where(function($q){
                $q->whereNull('result')->orWhere('result', 'no_answer');
            })
            ->orderBy('call_time', 'asc')
            ->limit(12)
            ->get();

        $recentActivities = UserActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'appointments', 'total', 'present', 'notArrived', 
            'newVisits', 'reviews',
            'callList', 'recentActivities', 'date', 
            'percentageChange', 'changeDirection'
        ));
    }

    public function appointments(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $status = $request->query('status');

        $query = Appointment::with('patient')->whereDate('date', $date);

        if ($status) {
            switch ($status) {
                case 'present':
                    $query->whereIn('status', ['queued', 'in_room', 'seen', 'present']);
                    break;
                case 'missed':
                    $query->where('status', 'missed');
                    break;
                case 'scheduled':
                    $query->where('status', 'scheduled');
                    break;
            }
        }

        $appointments = $query->orderBy('time')->get();

        return response()->json($appointments);
    }

    
    public function index(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        $patients = Patient::with([
                'appointments' => function ($q) {
                    // bring latest appointment for display (by date desc)
                    $q->orderByDesc('date')->orderByDesc('time')->limit(1);
                },
                'attendances' => function ($q) use ($date) {
                    // attendance only for the requested date
                    $q->where('date', $date);
                }
            ])->get();

        $data = $patients->map(function ($p) {
            $attendance = $p->attendances->first();
            $appt = optional($p->appointments->first());
            return [
                'id' => $p->id,
                'first_name' => $p->first_name,
                'last_name' => $p->last_name,
                'phone' => $p->phone,
                'appointment_time' => $appt->time ?? null,
                'is_present' => $attendance ? (bool) $attendance->is_present : false,
                'id_number' => $p->id_number,
                'hospital_number' => $p->hospital_number,
            ];
        });

        return response()->json($data);
    }

  public function search(Request $request)
{
    $term = trim((string) $request->query('term', ''));
    $date = $request->query('date'); // optional

    if ($term === '') {
        return response()->json([], 200);
    }

    // Normalize for matching against name_search (lowercased + collapsed spaces)
    $termNormalized = mb_strtolower(preg_replace('/\s+/', ' ', $term));
    $digits = preg_replace('/\D+/', '', $term);

    try {
        $patientsQuery = \App\Models\Patient::query();

        if ($date) {
            $patientsQuery->whereHas('appointments', function ($q) use ($date) {
                $q->whereDate('date', $date);
            });
        }

        // eager load latest appointment (prefer the requested date if supplied)
        $patientsQuery->with(['appointments' => function ($q) use ($date) {
            if ($date) $q->whereDate('date', $date);
            $q->orderBy('date', 'desc')->orderBy('time', 'desc')->limit(1);
        }]);

        // Search the plaintext helper columns only
        $patientsQuery->where(function ($q) use ($termNormalized, $digits, $term) {
            $q->where('name_search', 'like', "%{$termNormalized}%");

            if ($digits !== '') {
                $q->orWhere('phone_search', 'like', "%{$digits}%");
            }

            // fallback on non-encrypted identifiers
            $q->orWhere('hospital_number', 'like', "%{$term}%")
              ->orWhere('id_number', 'like', "%{$term}%");
        });

        $patients = $patientsQuery->limit(30)->get();

        $results = $patients->map(function ($p) {
            // try to decrypt for display; if decryption fails, fall back to name_search
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
            $apptTime = null; $apptDate = null;
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
                'first_name' => $first,
                'last_name'  => $last,
                'phone' => $phone,
                'hospital_number' => $p->hospital_number,
                'appointment_date' => $apptDate,
                'appointment_time' => $apptTime,
                'label' => $fullName,
            ];
        })->values();

        return response()->json($results, 200);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Dashboard search error', [
            'term' => $term,
            'date' => $date,
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json([], 200);
    }
}


}
