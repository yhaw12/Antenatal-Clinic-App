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
                ->whereIn('status', ['queued', 'in_room', 'seen'])
                ->count();
        }

        $notArrived = max(0, $total - $present);

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

        return view('dashboard', compact(
            'appointments', 'total', 'present', 'notArrived', 'callList', 'recentActivities'
        ));
    
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


}
