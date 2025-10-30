<?php
namespace App\Http\Controllers;

use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class UserActivityLogController extends Controller
{
    public function __construct()
    {
        // only admins should view logs — adapt to your policy/gate
        $this->middleware(['auth', 'can:view-activity-logs']);
    }

    /**
     * List logs with filters (user, action, date range).
     */
    public function index(Request $request)
    {
        $q = UserActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $q->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('action')) {
            $q->where('action', $request->input('action'));
        }
        if ($request->filled('from')) {
            $q->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('created_at', '<=', $request->input('to'));
        }

        $logs = $q->paginate(30)->withQueryString();

        $actions = UserActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.activity_logs.index', compact('logs','actions'));
    }


    /**
     * Show one log detail.
     */
    public function show(UserActivityLog $userActivityLog)
    {
        $this->authorize('view', $userActivityLog);

        return view('admin.activity_logs.show', ['log' => $userActivityLog]);
    }

    /**
     * Remove a log entry (admin-only).
     */
    public function destroy(UserActivityLog $userActivityLog)
    {
        $this->authorize('delete', $userActivityLog);
        $userActivityLog->delete();
        return redirect()->route('admin.activity-logs.index')->with('success','Log deleted.');
    }
}
