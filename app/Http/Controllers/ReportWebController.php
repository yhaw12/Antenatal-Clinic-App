<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;

class ReportWebController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        $data = $request->validate(['from' => 'required|date', 'to' => 'required|date']);
        $appts = Appointment::with('patient')->whereBetween('scheduled_date', [$data['from'],$data['to']])->get();
        // for now render on-screen; admin can export later
        return view('reports.generate', compact('appts','data'));
    }
}
