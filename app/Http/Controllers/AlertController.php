<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AlertController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only admins may create custom alerts
        // $this->middleware('role:admin')->only(['createCustom']);
    }

    /**
     * API: Return unread alerts for the current user.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return response()->json(['data' => [], 'count' => 0], 200);
            }

            $prefs = is_array($user->notification_preferences) ? $user->notification_preferences : [];
            $inApp = isset($prefs['in_app']) ? (bool) $prefs['in_app'] : true;
            if (! $inApp) {
                return response()->json(['data' => [], 'count' => 0], 200);
            }

            $limit = (int) $request->query('limit', 50);
            $criticalOnly = $request->boolean('critical_only', false);

            if ($user->hasRole('admin')) {
                $query = Alert::where('is_read', false);
            } else {
                $query = Alert::where('is_read', false)
                    ->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)->orWhereNull('user_id');
                    });
            }

            if ($criticalOnly) {
                $query->where('type', 'critical');
            }

            $totalUnread = $query->count();
            $alerts = $query->orderBy('created_at', 'desc')->limit($limit)->get();

            $payload = $alerts->map(function ($alert) {
                return [
                    'id' => (string) $alert->id,
                    'message' => $alert->message,
                    'type' => $alert->type,
                    'url' => $alert->url ?? null,
                    'is_read' => $alert->is_read,
                    'created_at' => optional($alert->created_at)->toDateTimeString(),
                    'human_time' => optional($alert->created_at)->diffForHumans(),
                ];
            });

            return response()->json(['data' => $payload, 'count' => $totalUnread], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch notifications', ['error' => $e->getMessage()]);
            return response()->json(['data' => [], 'count' => 0], 500);
        }
    }

    /**
     * Web view: show notifications page
     */
    public function view(Request $request)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return redirect()->route('login');
            }

            $query = $user->hasRole('admin')
                ? Alert::query()
                : Alert::where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhereNull('user_id');
                });

            $alerts = $query->orderBy('created_at', 'desc')->paginate(20);

            return view('notifications.index', compact('alerts'));
        } catch (\Throwable $e) {
            return view('notifications.index', ['alerts' => collect(), 'error' => 'Failed to load alerts']);
        }
    }

    /**
     * Mark a single alert as read.
     */
    public function read(Request $request, Alert $alert)
    {
        try {
            $user = Auth::user();
            
            // Check auth
            if (! $user->hasRole('admin') && $alert->user_id !== $user->id && !is_null($alert->user_id)) {
                if ($request->wantsJson()) return response()->json(['error' => 'Forbidden'], 403);
                abort(403);
            }

            $alert->update(['is_read' => true, 'read_at' => now()]);

            // --- CRITICAL FIX: Return Redirect if not AJAX ---
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true], 200);
            }

            return back()->with('success', 'Notification marked as read');

        } catch (\Throwable $e) {
            Log::error('Failed to mark alert read', ['error' => $e->getMessage()]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Failed'], 500);
            }
            return back()->with('error', 'Could not mark notification as read');
        }
    }

    /**
     * Dismiss all unread alerts.
     */
    public function dismissAll(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = $user->hasRole('admin')
                ? Alert::where('is_read', false)
                : Alert::where('is_read', false)
                    ->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)->orWhereNull('user_id');
                    });

            $query->update(['is_read' => true, 'read_at' => now()]);

            // --- CRITICAL FIX: Return Redirect if not AJAX ---
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true], 200);
            }

            return back()->with('success', 'All notifications cleared');

        } catch (\Throwable $e) {
            Log::error('Failed to dismiss alerts', ['error' => $e->getMessage()]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Failed'], 500);
            }
            return back()->with('error', 'Could not clear notifications');
        }
    }

    /**
     * Create a custom alert (admin-only).
     */
    public function createCustom(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'type' => 'required|in:info,warning,critical,success,inventory,sale,mortality,payment',
            'message' => 'required|string|max:1000',
            'url' => 'nullable|url',
        ]);

        try {
            $alertData = [
                'user_id' => $data['user_id'] ?? null,
                'type' => $data['type'],
                'message' => $data['message'],
                'url' => $data['url'] ?? null,
                'is_read' => false,
            ];

            // Handle UUID if necessary
            $alertModel = new Alert();
            if (! $alertModel->getIncrementing()) {
                $alertData['id'] = (string) Str::uuid();
            }

            $alert = Alert::create($alertData);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'alert_id' => $alert->id], 201);
            }
            return back()->with('success', 'Alert created');

        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Failed'], 500);
            }
            return back()->with('error', 'Failed to create alert');
        }
    }
}