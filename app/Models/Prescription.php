<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use SoftDeletes;

    protected $fillable = ['event_type', 'event_id', 'patient_id', 'status'];

    protected $casts = [
        'status' => Status::class,
    ];

    protected $with = ['lines'];

    protected $appends = [];

    public function event()
    {
        return $this->morphTo();
    }

    public function lines() {
        return $this->hasMany(PrescriptionLine::class, 'prescription_id');
    }

    public function patient() {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
