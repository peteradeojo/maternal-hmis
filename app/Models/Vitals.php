<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vitals extends Model
{
    use HasFactory;

    protected $fillable = [
        'recordable_type',
        'recordable_id',
        'blood_pressure',
        'weight',
        'temperature',
        'pulse',
        'respiration',
        'recording_user_id',
    ];

    public function recordable() {
        return $this->morphTo();
    }

    public function recorder() {
        return $this->belongsTo(User::class, 'recording_user_id');
    }
}
