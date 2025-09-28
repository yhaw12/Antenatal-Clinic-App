<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role; // used only if Spatie is installed

class UserController extends Controller
{
    public function __construct()
    {
        // Allow guests to view the login page and submit login (and temp register)
        $this->middleware('guest')->only(['showLoginForm', 'login', 'showTempRegisterForm', 'tempRegister']);

        // Only authenticated users can call logout and assignRole
        $this->middleware('auth')->only(['logout', 'assignRole']);
    }

    /**
     * Show the login form (guest only)
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login attempts
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            // Activity log (if available)
            try {
                if (class_exists(ActivityLogger::class)) {
                    ActivityLogger::log('login', $request, $user, [
                        'details' => 'User logged in'
                    ]);
                } elseif (class_exists(UserActivityLog::class)) {
                    UserActivityLog::create([
                        'user_id' => $user->id,
                        'action' => 'login',
                        'details' => 'User logged in',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Activity log failed on login: ' . $e->getMessage());
            }

            // Friendly status message: determine admin without assuming hasRole()
            $isAdmin = false;
            try {
                if (method_exists($user, 'hasRole')) { // spatie or custom trait
                    $isAdmin = $user->hasRole('admin');
                } elseif (method_exists($user, 'getRoleNames')) { // spatie API
                    $isAdmin = collect($user->getRoleNames())->contains('admin');
                } else {
                    // fallback: try roles relation if present and iterable
                    $roles = $user->roles ?? null;
                    if (is_iterable($roles)) {
                        $isAdmin = collect($roles)->pluck('name')->contains('admin');
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Role check failed during login: ' . $e->getMessage());
                $isAdmin = false;
            }

           session()->flash('status', $user->isAdmin() ? 'Logged in as Admin.' : 'Login successful.');

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Logout (auth only)
     */
    public function logout(Request $request)
    {
        try {
            if (class_exists(ActivityLogger::class) && Auth::check()) {
                ActivityLogger::log('logout', $request, auth()->user(), [
                    'details' => 'User logged out'
                ]);
            } elseif (class_exists(UserActivityLog::class) && Auth::check()) {
                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'logout',
                    'details' => 'User logged out',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Activity log failed on logout: ' . $e->getMessage());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show temporary registration form (only when enabled).
     */
    public function showTempRegisterForm()
    {
        if (!env('APP_ALLOW_TEMP_REGISTER', false)) {
            abort(404);
        }

        return view('auth.temp-register');
    }

    /**
     * Handle temporary registration (only when enabled and token matches).
     */
    public function tempRegister(Request $request)
    {
        if (!env('APP_ALLOW_TEMP_REGISTER', false)) {
            abort(403, 'Temporary registration is disabled.');
        }

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users'],
            'password' => ['required','string','min:8','confirmed'],
            'token' => ['required','string'],
        ]);

        // Validate registration token
        $expected = env('TEMP_REG_TOKEN');
        if (!$expected || !hash_equals($expected, $data['token'])) {
            return back()->withErrors(['token' => 'Invalid registration token.'])->onlyInput('name','email');
        }

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign default role (if Spatie installed and user supports assignRole)
        $defaultRole = env('TEMP_REG_ROLE', 'nurse');
        try {
            if (class_exists(Role::class) && method_exists($user, 'assignRole')) {
                $guard = config('auth.defaults.guard') ?? 'web';
                Role::firstOrCreate(['name' => $defaultRole, 'guard_name' => $guard]);
                $user->assignRole($defaultRole);
            } else {
                Log::info("Temporary user created (id={$user->id}) but role system not present.");
            }
        } catch (\Throwable $e) {
            Log::warning('Temp register role assignment failed: ' . $e->getMessage());
        }

        // Activity log (optional)
        try {
            if (class_exists(ActivityLogger::class)) {
                ActivityLogger::log('temp_register', $request, $user, [
                    'details' => 'Temporary registration used'
                ]);
            } elseif (class_exists(UserActivityLog::class)) {
                UserActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'temp_register',
                    'details' => 'Temporary registration used',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Activity log for temp register failed: ' . $e->getMessage());
        }

        // Auto-login
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created (temporary). Please complete your profile.');
    }

    /**
     * NOTE: public self-registration (permanent) is disabled in this controller.
     */
    public function showRegistrationForm()
    {
        return redirect()->route('login');
    }

    public function register(Request $request)
    {
        abort(403, 'Public registration is disabled. Only administrators can create accounts.');
    }

    /**
     * Assign a role to an existing user.
     * This will use Spatie if present; otherwise it logs a warning and returns gracefully.
     */
    public function assignRole(Request $request, User $user)
    {
        $this->authorize('assign-role');

        $request->validate(['role' => 'required|string']);

        $guardName = config('auth.defaults.guard', 'web') ?? 'web';
        $roleName = $request->input('role');

        try {
            if (class_exists(Role::class) && method_exists($user, 'assignRole')) {
                Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guardName]);
                $user->assignRole($roleName);
                return redirect()->back()->with('success', 'Role assigned.');
            } else {
                Log::warning("Attempted to assign role '{$roleName}' to user_id={$user->id} but role package not installed.");
                return redirect()->back()->with('warning', 'Role system not available; role not assigned.');
            }
        } catch (\Throwable $e) {
            Log::warning('Role assignment failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to assign role.');
        }
    }
}
