<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    use HasFactory;

    protected $table = 'call_logs';

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'called_by',
        'call_time',
        'result',
        'notes',
    ];

    protected $casts = [
        'call_time' => 'datetime',
    ];

    /**
     * The patient this call log is for.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * (Optional) link to appointment if you store appointment_id.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    /**
     * (Optional) user who made the call (if you store called_by as user id).
     */
    public function caller()
    {
        return $this->belongsTo(\App\Models\User::class, 'called_by');
    }
}
