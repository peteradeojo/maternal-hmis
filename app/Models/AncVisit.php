<?php

namespace App\Models;

use App\Interfaces\Visitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AncVisit extends Model implements Visitation
{
    use HasFactory;

    public const testsList = [
        'HIV',
        'PCV',
        'Edema',
        'VDRL',
        'Hepatitis B',
        'Blood Group',
        'Genotype',
        'Protein',
        'Glucose',
        'Pap Smear'
    ];

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'fundal_height',
        'fetal_heart_rate',
        'presentation',
        'lie',
        'presentation_relationship',
        'return_visit',
        'complaints',
        'drugs',
        'note',
        'antenatal_profile_id',
    ];

    protected $with = ['patient', 'doctor'];

    public function lab()
    {
    }

    public function pharmacy()
    {
    }

    public function tests() {
        return $this->morphMany(DocumentationTest::class, 'testable');
    }

    public function visit()
    {
        return $this->morphOne(Visit::class, 'visit');
    }

    public function profile() {
        return $this->belongsTo(AntenatalProfile::class, 'antenatal_profile_id');
    }

    public function getType(): string
    {
        return "Antenatal";
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
