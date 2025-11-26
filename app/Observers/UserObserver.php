<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user)
    {
        // 1. THE FIRST USER RULE
        // We check the total count of users. Since this event fires AFTER creation,
        // if the count is 1, this is the very first user.
        if (User::count() === 1) {
            $user->assignRole('admin');
            Log::info("System Bootstrap: User #{$user->id} ({$user->email}) automatically assigned as ADMIN.");
            return;
        }

        // 2. DEFAULT ROLE FOR EVERYONE ELSE
        // If the user wasn't assigned a role during creation (e.g. via the registration form),
        // give them the default safe role.
        if ($user->roles()->count() === 0) {
            $user->assignRole('midwife'); 
        }
    }
}