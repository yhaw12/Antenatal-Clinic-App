<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Patient;

class ReferralWebController extends Controller
{
    public function index()
    {
        // find visits with referrals
        $visits = Visit::with('patient')->whereNotNull('referral_to')->latest()->paginate(40);
        return view('referrals.index', compact('visits'));
    }

    public function create()
    {
        $patients = Patient::orderBy('first_name')->limit(200)->get();
        $chns = User::role('chns')->get(); // requires spatie roles; if not present adjust
        return view('referrals.create', compact('patients','chns'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            // 'appointment_id' is helpful to link it to the specific schedule
            'appointment_id' => 'nullable|exists:appointments,id', 
            'reason' => 'required|string|max:1000'
        ]);

        // Logic: Create a Visit record marked as a "Referral"
        // We use the 'complaints' column to store the referral notes/reason
        
        Visit::create([
            'patient_id' => $data['patient_id'],
            'user_id'    => $request->user()->id, // The staff member creating the referral
            'appointment_id' => $data['appointment_id'] ?? null,
            'arrived_at' => now(),
            'complaints' => "REFERRAL NOTES: " . $data['reason'], // Save notes here
            // 'referral_to' => ... (If you add a column for doctor ID later, add it here)
            // We set a flag or special status if your DB has one, e.g., is_referral = true
            // If you don't have a specific column, the text prefix helps identify it.
        ]);

        // Optional: Mark the appointment as 'seen' or 'completed' so it leaves the queue
        if (!empty($data['appointment_id'])) {
            Appointment::where('id', $data['appointment_id'])->update(['status' => 'seen']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Referral note saved']);
        }

        return back()->with('success', 'Referral recorded');
    }
}
