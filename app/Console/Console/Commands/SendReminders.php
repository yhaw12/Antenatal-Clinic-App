<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Notifications\AppointmentReminder;
use Illuminate\Support\Facades\Notification;

class SendReminders extends Command
{
    protected $signature = 'anc:send-reminders';
    protected $description = 'Queue SMS reminders for appointments happening tomorrow';

    public function handle()
    {
        $tomorrow = now()->addDay()->toDateString();
        $appts = Appointment::with('patient')->where('scheduled_date', $tomorrow)->get();
        foreach ($appts as $a) {
            if ($a->patient && $a->patient->phone) {
                Notification::route('mail', $a->patient->email ?? null)
                    ->notify(new AppointmentReminder($a)); // example; replace with SMS route
                $this->info('Queued reminder for appointment '.$a->id);
            }
        }
        return 0;
    }
}
