<?php

namespace App\Models;

use App\Models\Visit;
use App\Interfaces\Visitation;
use App\Interfaces\Documentable;
use App\Traits\Documentable as TraitsDocumentable;
use App\Traits\Visit as VisitTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AncVisit extends Model implements Documentable, Visitation
{
    use HasFactory, VisitTrait, TraitsDocumentable;

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
        'edema',
        'pcv',
        'vdrl',
        'protein',
        'glucose',
    ];

    protected $with = ['patient', 'doctor'];

    protected $appends = ['type'];

    public function complaints()
    {
        return $this->morphMany(DocumentationComplaints::class, 'documentable');
    }

    public function treatments()
    {
        return $this->morphMany(DocumentationPrescription::class, 'prescriptionable');
    }

    final public function radios()
    {
        return $this->morphMany(PatientImaging::class, 'documentable');
    }

    // final public function tests()
    // {
    //     return $this->morphMany(DocumentationTest::class, 'testable');
    // }

    public function visit()
    {
        return $this->morphOne(Visit::class, 'visit');
    }

    public function profile()
    {
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

    public function diagnoses()
    {
        return $this->morphMany(DocumentedDiagnosis::class, 'diagnosable');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    protected static function booted()
    {
        static::created(function (AncVisit $visit) {
            $test = Product::where('name', 'like',  '%ROUTINE ANTENATAL %')->first();
            if (!$test) {
                return;
            }

            $visit->tests()->create([
                'name' => $test->name,
                'describable_type' => $test::class,
                'describable_id' => $test->id,
                'patient_id' => $visit->patient->id,
            ]);
        });
    }

    public function notes()
    {
        return $this->morphMany(ConsultationNote::class, 'visit');
    }
}
