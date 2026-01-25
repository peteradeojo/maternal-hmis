<?php

namespace App\Models;

use App\Traits\CastsStatus;
use App\Traits\NeedsRecorderInfo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PatientAppointment extends Model
{
    use NeedsRecorderInfo, CastsStatus;

    const DOCTOR_SOURCE = 'doctor_scheduled';
    const RECORD_SOURCE = 'record_scheduled';

    protected $fillable = [
        'patient_id',
        'booked_by',
        'visit_id',
        'source',
        'status',
        'appointment_date',
        'note',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    public function notes()
    {
        return $this->morphMany(ConsultationNote::class, 'visit');
    }

    public function patient() {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function source_visit() {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function source(): Attribute {
        return Attribute::make(get: function ($value) {
            return match($value) {
                self::DOCTOR_SOURCE => 'Consultant',
                self::RECORD_SOURCE => 'Records',
                default => $value,
            };
        });
    }
}
