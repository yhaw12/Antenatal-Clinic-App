<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = ['appointment_id','patient_id','user_id','arrived_at','seen_at','complaints','bp','weight','referral_to','chns_feedback'];
}
