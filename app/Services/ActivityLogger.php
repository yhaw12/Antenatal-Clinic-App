<?php
namespace App\Services;

use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param  array|string  $action  Short action name or array with more options
     * @param  \Illuminate\Http\Request|null  $request
     * @param  \App\Models\User|null  $user
     * @return \App\Models\UserActivityLog
     *
     * Usage:
     * ActivityLogger::log('create_patient', $request, auth()->user(), [
     *   'model_type' => Patient::class, 'model_id' => $patient->id, 'details' => 'Created patient'
     * ]);
     */
    public static function log($action, ?Request $request = null, $user = null, array $options = [])
    {
        // normalise parameters
        $userId = $user ? ($user->id ?? $user) : (auth()->check() ? auth()->id() : null);

        $meta = $options['meta'] ?? [];
        if ($request instanceof Request) {
            $meta = array_merge($meta, [
                'route' => optional($request->route())->getName(),
                'method' => $request->method(),
                'payload_keys' => array_keys($request->except(['_token','password','password_confirmation'])),
            ]);
        }

        $log = UserActivityLog::create([
            'user_id'    => $userId,
            'action'     => is_array($action) ? ($action['name'] ?? null) : $action,
            'model_type' => $options['model_type'] ?? null,
            'model_id'   => $options['model_id'] ?? null,
            'details'    => $options['details'] ?? ($options['details'] ?? null),
            'meta'       => $meta ?: null,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
        ]);

        return $log;
    }
}
