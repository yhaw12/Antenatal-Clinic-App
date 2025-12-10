<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Attendance;
use App\Models\CallLog;
use App\Models\Patient;
use App\Models\UserActivityLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

    public function dashboard(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        
        // Dynamic Pagination Limit
        // Default to 15 (Desktop), but respect 'per_page' if sent by frontend
        $perPage = $request->query('per_page', 15); 

        // 1. Fetch Appointments with Pagination
        $appointmentsQuery = Appointment::with('patient')
            ->whereDate('date', $date)
            ->orderBy('time');

        $appointments = $appointmentsQuery->paginate($perPage);

        // 2. Calculate KPIs for Initial Load
        $allAppts = Appointment::with('patient')->whereDate('date', $date)->get();
        $total = $allAppts->count();
        
        $present = Attendance::whereDate('date', $date)->where('is_present', true)->count();
        if ($present === 0) {
            $present = $allAppts->whereIn('status', ['queued', 'in_room', 'seen', 'present'])->count();
        }
        $notArrived = max(0, $total - $present);

        $newVisits = $allAppts->filter(function($appt) use ($date) {
            return $appt->patient && $appt->patient->created_at->isSameDay(Carbon::parse($date));
        })->count();
        $reviews = max(0, $total - $newVisits);

        // 3. Percentage Change
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
                case 'present': $query->whereIn('status', ['queued', 'in_room', 'seen', 'present']); break;
                case 'missed': $query->where('status', 'missed'); break;
                case 'scheduled': $query->where('status', 'scheduled'); break;
            }
        }
        return response()->json($query->orderBy('time')->get());
    }

    public function search(Request $request)
    {
        $term = trim((string) $request->query('term', ''));
        $date = $request->query('date'); 

        if ($term === '') {
            return response()->json([], 200);
        }

        // --- SECURITY: Check if user is Admin ---
        // Adjust 'admin' to whatever role name you use in your system
        $isAdmin = $request->user() && ($request->user()->hasRole('admin') || $request->user()->is_admin); 
        $termNormalized = mb_strtolower(preg_replace('/\s+/', ' ', $term));
        $digits = preg_replace('/\D+/', '', $term);

        try {
            $patientsQuery = Patient::query();

            if ($date) {
                $patientsQuery->whereHas('appointments', function ($q) use ($date) {
                    $q->whereDate('date', $date);
                });
            }else {
            // Fallback: Just get latest appointment if no date context exists
            $patientsQuery->with(['appointments' => function ($q) {
                $q->orderBy('date', 'desc')->orderBy('time', 'desc')->limit(1);
            }]);
        }

            $patientsQuery->with(['appointments' => function ($q) use ($date) {
                if ($date) $q->whereDate('date', $date);
                $q->orderBy('date', 'desc')->orderBy('time', 'desc')->limit(1);
            }]);

            $patientsQuery->where(function ($q) use ($termNormalized, $digits, $term) {
                $q->where('name_search', 'like', "%{$termNormalized}%");
                if ($digits !== '') {
                    $q->orWhere('phone_search', 'like', "%{$digits}%");
                }
                $q->orWhere('hospital_number', 'like', "%{$term}%")
                  ->orWhere('id_number', 'like', "%{$term}%");
            });

            $patients = $patientsQuery->limit(30)->get();

            $results = $patients->map(function ($p) use ($isAdmin) {
                // 1. Decrypt Data
                $first = null; $last = null; $phone = null;
                try { $first = $p->first_name; } catch (\Throwable $e) { }
                try { $last  = $p->last_name;  } catch (\Throwable $e) { }
                try { $phone = $p->phone;      } catch (\Throwable $e) { }

                // 2. Handle Name
                if (empty($first) && empty($last) && !empty($p->name_search)) {
                    $parts = preg_split('/\s+/', $p->name_search);
                    $parts = array_map(function ($n) { return mb_convert_case($n, MB_CASE_TITLE, "UTF-8"); }, $parts);
                    $first = $parts[0] ?? null;
                    $last  = count($parts) > 1 ? $parts[count($parts)-1] : null;
                }

                // --- 3. MASK PHONE NUMBER (Security Logic) ---
                if ($phone && !$isAdmin) {
                    $len = strlen($phone);
                    if ($len > 5) {
                        // Show 1st digit, mask middle, show last 4
                        $phone = substr($phone, 0, 1) . str_repeat('*', $len - 5) . substr($phone, -4);
                    } else {
                        $phone = '******';
                    }
                }

                // 4. Appt Info
                $appt = optional($p->appointments->first());
                $apptTime = null; $apptDate = null;
                if ($appt && $appt->time) {
                    try { $apptTime = Carbon::parse($appt->time)->format('h:i A'); } catch (\Throwable $e) { $apptTime = $appt->time; }
                }
                if ($appt && $appt->date) {
                    try { $apptDate = Carbon::parse($appt->date)->toDateString(); } catch (\Throwable $e) { $apptDate = $appt->date; }
                }

                $fullName = trim(($first ?? '') . ' ' . ($last ?? ''));
                if (!$fullName) {
                    $fullName = $p->name_search ? mb_convert_case($p->name_search, MB_CASE_TITLE, "UTF-8") : 'Unknown';
                }

                return [
                    'id' => $p->id,
                    'first_name' => $first,
                    'last_name'  => $last,
                    'phone' => $phone, // Will be masked for non-admins
                    'hospital_number' => $p->hospital_number,
                    'appointment_date' => $appt ? $appt->date : null, 
                    'appointment_time' => $apptTime,
                    'label' => $fullName,
                ];
            })->values();

            return response()->json($results, 200);
        } catch (\Throwable $e) {
            Log::error('Dashboard search error', [
                'term' => $term,
                'error' => $e->getMessage()
            ]);
            return response()->json([], 200);
        }
    }
}