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
        return Visit::with(['patient', 'visit'])->whereNotIn('status', [Status::closed->value, Status::completed->value, Status::blocked->value])->where(function ($query) {
            $query->whereHas('visit', function ($query) {
                $query->doesntHave('vitals');
            });
        })->latest();
    }
}
