<?php
namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReferralController extends Controller
{
    public function referToChns(Request $request, Visit $visit)
    {
        try {
            $this->authorize('create-referral', $visit); // Assumes a policy exists

            $data = $request->validate([
                'referred_to_user_id' => 'required|exists:users,id',
                'reason' => 'required|string|max:1000',
            ]);

            $visit->update(['referral_to' => $data['referred_to_user_id'], 'complaints' => $data['reason']]);

            return response()->json(['ok' => true, 'message' => 'Referral created']);
        } catch (\Exception $e) {
            Log::error('Failed to create referral', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create referral'], 500);
        }
    }

    public function addFeedback(Request $request, Visit $visit)
    {
        try {
            $this->authorize('add-feedback', $visit); // Assumes a policy exists

            $data = $request->validate([
                'chns_feedback' => 'required|string|max:1000',
            ]);

            $visit->update(['chns_feedback' => $data['chns_feedback']]);

            return response()->json(['ok' => true, 'message' => 'Feedback added']);
        } catch (\Exception $e) {
            Log::error('Failed to add feedback', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to add feedback'], 500);
        }
    }
}