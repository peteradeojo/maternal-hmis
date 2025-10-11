<?php

namespace App\Models;

use App\Interfaces\Documentable as InterfacesDocumentable;
use App\Traits\Documentable;
use App\Traits\HasVisitData;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Admission extends Model implements InterfacesDocumentable
{
    use HasFactory, Documentable, HasVisitData;

    protected $guarded = [];

    protected $with = ['ward', 'patient', 'plan'];

    protected $appends = ['in_ward'];

    public function visit() {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function inWard(): Attribute
    {
        return Attribute::make(get: fn () => isset($this->ward_id));
    }

    public function admittable()
    {
        return $this->morphTo();
    }

    public function vitals()
    {
        return $this->morphMany(Vitals::class, 'recordable');
    }

    public function administrations()
    {
        return  $this->hasMany(AdmissionTreatments::class)->latest();
    }

    public function plans()
    {
        return $this->hasMany(AdmissionPlan::class)->latest();
    }

    public function tests() {
        return $this->morphMany(DocumentationTest::class, 'testable');
    }

    public function plan()
    {
        return $this->hasOne(AdmissionPlan::class)->latest();
    }
}
