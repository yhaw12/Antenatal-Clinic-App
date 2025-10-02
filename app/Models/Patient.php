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
        // Use the model accessors: encrypted casts decrypt on get, and if the properties were just set
        // they will be plain values — this approach avoids calling decrypt() directly (which can throw).
        $plainFirst = $patient->first_name ?? '';
        $plainLast  = $patient->last_name ?? '';

        // Build normalized name search: lowercase, collapse whitespace
        $plainName = trim("{$plainFirst} {$plainLast}");
        $patient->name_search = $plainName ? mb_strtolower(preg_replace('/\s+/', ' ', $plainName)) : null;

        // Normalize phone: remove non-digits
        $phone = $patient->phone ?? null;
        if ($phone) {
            $digits = preg_replace('/\D+/', '', $phone);
            $patient->phone_search = $digits ?: null;
        } else {
            $patient->phone_search = null;
        }
    });
}

}
