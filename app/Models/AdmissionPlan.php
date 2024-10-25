<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionPlan extends Model
{
    use HasFactory;

    protected $fillable  = [
        'admission_id',
        'user_id', 'indication', 'note'
    ];

    public function admission() {
        return $this->belongsTo(Admission::class);
    }

    public function treatments() {
        return $this->morphMany(DocumentationPrescription::class, 'event');
    }

    public function tests() {
        return $this->morphMany(DocumentationTest::class, 'testable');
    }

    public function scans() {
        return $this->morphMany(PatientImaging::class, 'documentable');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
