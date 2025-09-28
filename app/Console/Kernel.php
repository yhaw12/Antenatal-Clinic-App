<?php
namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        // Nightly backup at 02:00
        $schedule->command('backup:run --only-db')->dailyAt('02:00');

        // Daily SMS reminder job at 08:00 (implement the command)
        $schedule->command('anc:send-reminders')->dailyAt('08:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
