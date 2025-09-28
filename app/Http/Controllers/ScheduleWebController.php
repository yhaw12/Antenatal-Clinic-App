<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleWebController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        // pass initial filters (doctors, clinics, procedures) if needed
        // For demo we return an empty set and the JS will request the API
        return view('schedule.manager', compact('date'));
    }
}
