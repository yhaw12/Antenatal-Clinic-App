<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name','last_name','folder_no','phone','whatsapp','room',
        'next_of_kin_name','next_of_kin_phone','id_number','hospital_number',
        'next_review_date','address','complaints',
        // only these helper columns (you said you prefer these)
        'name_search','phone_search',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'last_name'  => 'encrypted',
        'phone'      => 'encrypted',
        'whatsapp'   => 'encrypted',
        'next_of_kin_name'  => 'encrypted',
        'next_of_kin_phone' => 'encrypted',
        'next_review_date' => 'date',
    ];

    // relations
    public function appointments() { return $this->hasMany(Appointment::class); }
    public function attendances()  { return $this->hasMany(Attendance::class); }
    public function callLogs()     { return $this->hasMany(CallLog::class); }


    /**
     * Maintain only name_search and phone_search on save.
     * Safe: if decryption fails, we do not overwrite existing helpers.
     */
    protected static function booted()
    {
        static::saving(function ($patient) {
            try {
                $plainFirst = (string) ($patient->first_name ?? '');
                $plainLast  = (string) ($patient->last_name ?? '');
                $plainPhone = $patient->phone ?? null;
            } catch (\Throwable $e) {
                // decryption failed (APP_KEY mismatch) — don't overwrite helpers
                return;
            }

            // Build a normalized full name
            $full = trim(preg_replace('/\s+/', ' ', "{$plainFirst} {$plainLast}"));

            // Transliterate accents -> ASCII when possible (improves matching)
            if (function_exists('transliterator_transliterate')) {
                $full = transliterator_transliterate('Any-Latin; Latin-ASCII;', $full);
            } else {
                $converted = @iconv('UTF-8', 'ASCII//TRANSLIT', $full);
                if ($converted) $full = $converted;
            }

            // Normalize: lowercase + collapse spaces + strip punctuation
            $full = mb_strtolower(trim(preg_replace('/\s+/', ' ', $full)));
            $full = str_replace(['.', ',', '-', "'", '"', '/'], '', $full);

            $patient->name_search = $full !== '' ? $full : null;

            // Phone digits-only
            $patient->phone_search = $plainPhone ? preg_replace('/\D+/', '', $plainPhone) : null;
        });
    }
    

    /**
     * Simple scope for searching
     */
    public function scopeSearch($query, $term)
    {
        $term = (string) $term;
        if ($term === '') return $query;

        $normalized = mb_strtolower(trim(preg_replace('/\s+/', ' ', $term)));
        $digits = preg_replace('/\D+/', '', $term);

        return $query->where(function ($q) use ($normalized, $digits, $term) {
            $q->where('name_search', 'like', "%{$normalized}%");
            if ($digits !== '') {
                $q->orWhere('phone_search', 'like', "%{$digits}%");
            }
            $q->orWhere('hospital_number', 'like', "%{$term}%")
              ->orWhere('id_number', 'like', "%{$term}%");
        });
    }
}
