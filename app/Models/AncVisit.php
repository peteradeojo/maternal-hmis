<?php

namespace App\Models;

use App\Enums\Department;
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
        'presentation_relationship',
        'edema',
        'note',
        'return_visit',
        'lie',
        'complaints',
        'drugs',
        'antenatal_profile_id',
        'pcv',
        'vdrl',
        'protein',
        'glucose',
        'tt',
        'ipt',
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

    final public function visit()
    {
        return $this->morphOne(Visit::class, 'visit');
    }

    protected static function booted()
    {
        static::saving(function (Self $visit) {
            if ($visit->isDirty('ipt') && $visit->ipt == true) {
                notifyDepartment(Department::NUR->value, "Immunization [IPT] for {{$visit->patient->name}}");
            } 

            if ($visit->isDirty('tt') && $visit->tt == true) {
                notifyDepartment(Department::NUR->value, "Immunization [TT] for {{$visit->patient->name}}");
            }
        });
    }
}
