<?php

namespace App\Models;

use App\Enums\Status;
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
        'recorded_date',
        'spo2',
        'fetal_heart_rate',
    ];

    protected $casts = [
        'recorded_date' => 'datetime',
    ];

    public function recordable()
    {
        return $this->morphTo();
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recording_user_id');
    }

    public static function getPendingVitalVisits()
    {
        return Visit::with(['patient.category', 'visit'])
        // ->whereNotIn('status', [Status::closed->value, Status::ejected->value, Status::completed->value, Status::blocked->value])
        ->doesntHave('vitals')->latest();
    }
}
