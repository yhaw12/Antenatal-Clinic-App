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
        // Ensure user is authenticated for these actions (adjust as needed)
        $this->middleware('auth');
        // Only admins may create custom alerts
        $this->middleware('role:admin')->only(['createCustom']);
    }

    /**
     * API: Return unread alerts for the current user (or all unread for admins).
     * Now returns alerts for the user OR global alerts (user_id IS NULL).
     * Query params:
     * - limit (optional)
     * - critical_only=1 (optional)
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                // If not authenticated, return empty list (JS will show zero). Avoid 401 to keep topbar simple.
                return response()->json(['data' => [], 'count' => 0], 200);
            }

            // If user's preferences disable in-app notifications, honor that
            $prefs = is_array($user->notification_preferences) ? $user->notification_preferences : [];
            $inApp = isset($prefs['in_app']) ? (bool) $prefs['in_app'] : true;
            if (! $inApp) {
                return response()->json(['data' => [], 'count' => 0], 200);
            }

            $limit = (int) $request->query('limit', 50);
            $limit = $limit > 0 && $limit <= 200 ? $limit : 50;

            // Default to showing ALL types (not just critical) for the bell – override prefs for broader visibility
            $criticalOnly = $request->boolean('critical_only', false); // Changed: default false, ignore prefs here

            // Build the base query for unread alerts
            if ($user->hasRole('admin')) {
                $query = Alert::where('is_read', false);
            } else {
                // user-specific OR global (user_id IS NULL)
                $query = Alert::where('is_read', false)
                    ->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)->orWhereNull('user_id');
                    });
            }

            if ($criticalOnly) {
                $query->where('type', 'critical');
            }

            // Get TOTAL unread count (unlimited, for accurate badge)
            $totalUnread = $query->count();

            // Get limited alerts for preview
            $alerts = $query->orderBy('created_at', 'desc')->limit($limit)->get();

            $payload = $alerts->map(function ($alert) {
                return [
                    'id' => (string) $alert->id,
                    'message' => $alert->message,
                    'type' => $alert->type,
                    'url' => $alert->url ?? null,
                    'is_read' => $alert->is_read, // Added: explicit for JS consistency
                    'created_at' => optional($alert->created_at)->toDateTimeString(),
                    'human_time' => optional($alert->created_at)->diffForHumans(), // Added: for better UX
                ];
            });

            // Log if zero but DB has unread to help debugging (optional) – now user-specific
            if ($totalUnread === 0) {
                Log::debug('index: user has 0 unread alerts', [
                    'user_id' => $user->id,
                    'prefs' => $prefs,
                ]);
            }

            return response()->json(['data' => $payload, 'count' => $totalUnread], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => optional(Auth::user())->id,
            ]);
            return response()->json(['data' => [], 'count' => 0], 500);
        }
    }

    /**
     * Web view: show notifications page (Blade)
     * Route name: notifications.index
     */
    public function view(Request $request)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return redirect()->route('login');
            }

            // For the full view, fetch ALL alerts (read + unread), paginated
            $query = $user->hasRole('admin')
                ? Alert::query()
                : Alert::where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhereNull('user_id');
                });

            // Respect prefs for the full page (unlike bell)
            $prefs = is_array($user->notification_preferences) ? $user->notification_preferences : [];
            $criticalOnly = isset($prefs['critical_only']) ? (bool) $prefs['critical_only'] : false;
            if ($criticalOnly) {
                $query->where('type', 'critical');
            }

            $alerts = $query->orderBy('created_at', 'desc')->paginate(20);

            return view('notifications.index', compact('alerts'));
        } catch (\Throwable $e) {
            Log::error('Failed to load alerts view', [
                'error' => $e->getMessage(), 'trace' => $e->getTraceAsString(),
                'user_id' => optional(Auth::user())->id,
            ]);
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
            if (! $user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Admins can mark any; regular users only their own or global
            if (! $user->hasRole('admin') && $alert->user_id !== $user->id && !is_null($alert->user_id)) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $alert->update(['is_read' => true, 'read_at' => now()]);
            return response()->json(['success' => true], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to mark alert read', [
                'error' => $e->getMessage(),
                'alert_id' => $alert->id,
                'user_id' => optional(Auth::user())->id,
            ]);
            return response()->json(['error' => 'Failed to mark alert as read'], 500);
        }
    }

    /**
     * Dismiss all unread alerts for the current user (or all if admin).
     */
    public function dismissAll(Request $request)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $query = $user->hasRole('admin')
                ? Alert::where('is_read', false)
                : Alert::where('is_read', false)
                    ->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)->orWhereNull('user_id');
                    });

            $updated = $query->update(['is_read' => true, 'read_at' => now()]);
            return response()->json(['success' => true, 'dismissed' => $updated], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to dismiss all alerts', [
                'error' => $e->getMessage(),
                'user_id' => optional(Auth::user())->id,
            ]);
            return response()->json(['error' => 'Failed to dismiss alerts'], 500);
        }
    }

    /**
     * Create a custom alert (admin-only). JSON response.
     * Required: type (info|warning|critical), message. Optional: user_id, url
     */
    public function createCustom(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'type' => 'required|in:info,warning,critical,success,inventory,sale,mortality,backup_success,backup_failed,payment', // Updated enum
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

            // If model uses non-incrementing PK (UUID), set id
            $alertModel = new Alert();
            if (! $alertModel->getIncrementing()) {
                $alertData['id'] = (string) Str::uuid();
            }

            $alert = Alert::create($alertData);
            return response()->json(['success' => true, 'alert_id' => $alert->id], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to create custom alert', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $data,
            ]);
            return response()->json(['error' => 'Failed to create custom alert'], 500);
        }
    }
}