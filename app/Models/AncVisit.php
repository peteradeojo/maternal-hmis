<?php

namespace App\Models;

use App\Dto\PrescriptionDto;
use App\Models\Visit;
use App\Interfaces\Visitation;
use App\Interfaces\Documentable;
use App\Interfaces\OperationalEvent;
use App\Traits\Documentable as TraitsDocumentable;
use App\Traits\HasVisitData;
use App\Traits\Visit as VisitTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AncVisit extends Model implements Documentable, Visitation, OperationalEvent
{
    use HasFactory, VisitTrait, TraitsDocumentable, HasVisitData;

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

    public function profile()
    {
        return $this->belongsTo(AntenatalProfile::class, 'antenatal_profile_id');
    }

    public function getType(): string
    {
        return "Antenatal";
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
}
