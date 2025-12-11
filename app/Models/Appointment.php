<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id','date','time','status','notes'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function callLogs()
    {
        return $this->hasMany(CallLog::class);
    }

   

//     public static function getNextAvailableSlot($date)
// {
//     $startTime = \Carbon\Carbon::parse('08:30:00');
//     $endTime = \Carbon\Carbon::parse('16:00:00');
//     $intervalMinutes = 10; // Adjust this (e.g., 15, 20, 30 mins per patient)

//     // Get the latest appointment time for this date
//     $latestAppt = self::whereDate('date', $date)
//                       ->orderByDesc('time')
//                       ->first();

//     if (!$latestAppt || !$latestAppt->time) {
//         // No appointments yet, start at 8:30
//         return $startTime->format('H:i:s');
//     }

//     // Calculate next slot
//     $lastTime = \Carbon\Carbon::parse($latestAppt->time);
//     $nextSlot = $lastTime->addMinutes($intervalMinutes);

//     // If next slot is past 4 PM, you might want to return null or just cap it
//     if ($nextSlot->gt($endTime)) {
//         // Option A: Return null to indicate "Day Full"
//         return null; 
//         // Option B: Just squeeze them in at 4 PM or keep adding (overtime)
//         // return $nextSlot->format('H:i:s'); 
//     }

//     return $nextSlot->format('H:i:s');
// }
}
