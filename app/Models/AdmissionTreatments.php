<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionTreatments extends Model
{
    use HasFactory, HasTimestamps;

    protected $table = "admission_treatment_administrations";

    protected $guarded = [];

    protected $touches = ['admission'];

    protected $with = ['minister', 'treatments'];

    public function admission() {
        return $this->belongsTo(Admission::class);
    }

    public function minister() {
        return $this->belongsTo(User::class, 'minister_id');
    }

    public function treatments() {
        return $this->belongsTo(DocumentationPrescription::class, 'treatment_id');
    }
}
