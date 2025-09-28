<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\User;
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
            'visit_id' => 'nullable|exists:visits,id',
            'referred_to_user_id' => 'required|exists:users,id',
            'reason' => 'required|string'
        ]);

        // create or update a visit referral
        if($data['visit_id']){
            $visit = Visit::find($data['visit_id']);
            $visit->update(['referral_to' => $data['referred_to_user_id']]);
        } else {
            // create a minimal visit with referral
            Visit::create([
                'appointment_id' => null,
                'patient_id' => $data['patient_id'],
                'user_id' => $request->user()->id,
                'arrived_at' => now(),
                'referral_to' => $data['referred_to_user_id'],
                'complaints' => $data['reason']
            ]);
        }

        return redirect()->route('referrals.index')->with('success','Referral recorded');
    }
}
