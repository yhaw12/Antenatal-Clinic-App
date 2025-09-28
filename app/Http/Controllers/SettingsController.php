<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Normalise stored preferences (could be JSON string or array)
        $stored = $user->preferences ?? [];
        if (is_string($stored)) {
            $decoded = json_decode($stored, true);
            $stored = is_array($decoded) ? $decoded : [];
        }

        // Defaults (no email here — only in_app + theme)
        $defaults = [
            'notifications' => [
                'in_app' => true,
            ],
            'theme' => 'system',
        ];

        // Merge stored over defaults (stored values override defaults)
        $preferences = array_replace_recursive($defaults, is_array($stored) ? $stored : []);

        return view('settings.index', compact('preferences'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate. Use 'sometimes' for notifications.in_app because checkbox may be absent when unchecked.
        $validated = $request->validate([
            'notifications.in_app' => 'sometimes|boolean',
            'theme' => 'required|in:light,dark,system',
        ]);

        // Read boolean safely (handles checked/unchecked)
        $inApp = $request->boolean('notifications.in_app', true); // default true if missing
        $theme = $validated['theme'];

        $preferences = [
            'notifications' => [
                'in_app' => $inApp,
            ],
            'theme' => $theme,
        ];

        try {
            // Save as array (Eloquent will cast to JSON if attribute is cast in model)
            $user->preferences = $preferences;
            $user->save();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'preferences' => $preferences], 200);
            }

            return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to update settings', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to save settings'], 500);
            }

            return back()->with('error', 'Failed to save settings.');
        }
    }
}
