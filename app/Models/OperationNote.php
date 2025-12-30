<?php

namespace App\Models;

use App\Interfaces\PatientRecord;
use App\Traits\NeedsRecorderInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperationNote extends Model implements PatientRecord
{
    use SoftDeletes, NeedsRecorderInfo;

    protected $fillable = [
        'unit',
        'consultant',
        'operation_date',
        'surgeons',
        'assistants',
        'scrub_nurse',
        'circulating_nurse',
        'anaesthesists',
        'anaesthesia_type',
        'indication',
        'incision',
        'findings',
        'procedure',
        'patient_id',
        'admission_id',
        'user_id',
    ];

    protected $with = ['recorder'];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function admission() {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }
}
