<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
    User::observe(UserObserver::class);
}

protected $listen = [
    \Illuminate\Auth\Events\Login::class => [
        \App\Listeners\RecordLastLogin::class,
    ],
];

}
