<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class RecordLastLogin
{
    public function handle(Login $event)
    {
        $user = $event->user;
        $user->forceFill(['last_login_at' => now()])->save();
    }
}