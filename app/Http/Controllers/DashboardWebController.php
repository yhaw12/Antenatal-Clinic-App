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
        $date = $request->query('date', \Carbon\Carbon::today()->toDateString());
        $status = $request->query('status', '');

        $appointmentsQuery = \App\Models\Appointment::whereDate('date', $date);

        if ($status) {
            switch ($status) {
                case 'present':
                    $appointmentsQuery->whereIn('status', ['queued', 'in_room', 'seen', 'present']);
                    break;
                case 'missed':
                    $appointmentsQuery->where('status', 'missed');
                    break;
                case 'scheduled':
                    $appointmentsQuery->where('status', 'scheduled');
                    break;
            }
        }

        $total = $appointmentsQuery->count();

        if ($status) {
            $present = ($status === 'present') ? $total : 0;
            $notArrived = ($status === 'missed') ? $total : 0;
        } else {
            $present = \App\Models\Attendance::whereDate('date', $date)->where('is_present', true)->count();
            if ($present === 0) {
                $present = \App\Models\Appointment::whereDate('date', $date)
                    ->whereIn('status', ['queued', 'in_room', 'seen', 'present'])
                    ->count();
            }
            $notArrived = max(0, $total - $present);
        }

        return response()->json(['total' => $total, 'present' => $present, 'notArrived' => $notArrived]);
    }

    // dashboard shows KPIs and quick link to daily queue
    public function dashboard(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        // Appointments for the selected date (paged for the UI)
        $appointmentsQuery = Appointment::with('patient')
            ->whereDate('date', $date)
            ->orderBy('time');

        $appointments = $appointmentsQuery->paginate(20);

        // KPI: total appointments
        $total = (int) $appointmentsQuery->count();

        // Determine present using Attendance table (preferred), fallback to appointment.status
        $present = Attendance::whereDate('date', $date)->where('is_present', true)->count();

        // fallback: if attendance table empty, compute from appointment status where queued/in_room/seen
        if ($present === 0) {
            $present = Appointment::whereDate('date', $date)
                ->whereIn('status', ['queued', 'in_room', 'seen', 'present'])
                ->count();
        }

        $notArrived = max(0, $total - $present);

        // compute simple percentage change vs yesterday (safe)
        $yesterday = Carbon::parse($date)->subDay()->toDateString();
        $yesterdayTotal = (int) Appointment::whereDate('date', $yesterday)->count();

        if ($yesterdayTotal === 0) {
            // if yesterday had zero appointments:
            if ($total === 0) {
                $percentageChange = 0;
                $changeDirection = ''; // no direction
            } else {
                // treat as +100% (or you can choose 'n/a')
                $percentageChange = 100;
                $changeDirection = '+';
            }
        } else {
            $diff = $total - $yesterdayTotal;
            $percentageChange = (int) round(($diff / $yesterdayTotal) * 100);
            $changeDirection = $percentageChange === 0 ? '' : ($percentageChange > 0 ? '+' : '-');
            $percentageChange = abs($percentageChange);
        }

        // Call list: pending call logs (example: we treat logs with result == null or special flag)
        $callList = CallLog::with('patient')
            ->where(function($q){
                $q->whereNull('result')->orWhere('result', 'no_answer');
            })
            ->orderBy('call_time', 'asc')
            ->limit(12)
            ->get();

        // Recent activities (replace with your activity/log model). Keep last 10.
        $recentActivities = UserActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Pass everything the view expects (including new changeDirection & percentageChange)
        return view('dashboard', compact(
            'appointments',
            'total',
            'present',
            'notArrived',
            'callList',
            'recentActivities',
            'date',
            'percentageChange',
            'changeDirection'
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
