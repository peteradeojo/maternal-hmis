<?php

namespace App\Models;

use App\Traits\CastsStatus;
use App\Traits\NeedsRecorderInfo;
use Illuminate\Database\Eloquent\Model;

class PatientAppointment extends Model
{
    use NeedsRecorderInfo, CastsStatus;

    protected $fillable = [
        'patient_id',
        'booked_by',
        'visit_id',
        'source',
        'status',
        'appointment_date',
        'note',
    ];

    public function notes()
    {
        return $this->morphMany(ConsultationNote::class, 'visit');
    }

    public function patient() {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
