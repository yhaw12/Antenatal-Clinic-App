<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name','last_name','folder_no','phone','whatsapp','room',
        'next_of_kin_name','next_of_kin_phone','id_number','hospital_number',
        'next_review_date','address','complaints',
        // search helpers
        'name_search','phone_search',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'last_name' => 'encrypted',
        'folder_no' => 'encrypted',
        'phone' => 'encrypted',
        'whatsapp' => 'encrypted',
        'next_of_kin_name' => 'encrypted',
        'next_of_kin_phone' => 'encrypted',
        'next_review_date' => 'date',
    ];

    // relations...
    public function appointments() {
         return $this->hasMany(Appointment::class);
     }
    public function attendances() {
         return $this->hasMany(Attendance::class); 
    }

    /**
     * Automatically maintain name_search and phone_search when saving.
     * name_search: lowercased concat of first & last name; phone_search: digits-only phone.
     */
    protected static function booted()
    {
        static::saving(function ($patient) {
            $plainName = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
            // normalize: lowercase and collapse whitespace
            $patient->name_search = $plainName ? mb_strtolower(preg_replace('/\s+/', ' ', $plainName)) : null;

            // phone might be set as unformatted numeric string; standardize digits
            $phone = $patient->phone ?? null;
            if ($phone) {
                // remove non digits
                $digits = preg_replace('/\D+/', '', $phone);
                $patient->phone_search = $digits ?: null;
            }
        });
    }
}
