<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    public function __construct()
    {
        // Require auth and admin role for all actions in this controller
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Show form to create a new user (admin-only)
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store new user and send password reset link so user sets own password
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:191',
            'email' => 'required|email|unique:users,email',
            'role'  => 'required|string'
        ]);

        // Create user with a random temp password (never used by user)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(32)),
        ]);

        // Ensure role exists and assign
        $guardName = config('auth.defaults.guard', 'web') ?? 'web';
        $role = Role::firstOrCreate(['name' => $request->role, 'guard_name' => $guardName]);
        $user->assignRole($role->name);

        // Send password reset link so user sets their own password
        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('admin.users.create')->with('success', 'User created and password reset email sent.');
        }

        return redirect()->route('admin.users.create')->with('warning', 'User created but failed to send password reset email. Please resend manually.');
    }
}
