<?php

namespace App\Models;

use App\Enums\NoteCodes;
use App\Enums\Status;
use App\Interfaces\OperationalEvent;
use App\Traits\Documentable;
use App\Traits\HasVisitData;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

class Admission extends Model implements OperationalEvent
{
    use HasFactory, Documentable, HasVisitData, SoftDeletes;

    protected $guarded = [];

    protected $with = ['patient', 'plan'];

    protected $appends = ['in_ward'];

    protected $casts = [
        'discharged_on' => 'datetime',
    ];

    public function visit()
    {
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
        return Attribute::make(get: fn() => isset($this->ward_id));
    }

    public function admittable()
    {
        return $this->morphTo();
    }

    // final public function vitals() {
    //     return $this->morphMany(Vitals::class, 'recordable');
    // }

    public function administrations()
    {
        return $this->hasMany(AdmissionTreatments::class)->latest();
    }

    public function plans()
    {
        return $this->hasMany(AdmissionPlan::class)->latest();
    }

    public function plan()
    {
        return $this->hasOne(AdmissionPlan::class)->latest();
    }

    public function reviews()
    {
        $query = $this->notes()->where(function ($q) {
            $q->where('code', '=', NoteCodes::AdmissionReview->value)->orWhereNull('code');
        });

        return $query;
    }

    public function operation_notes()
    {
        return $this->hasMany(OperationNote::class, 'admission_id')->latest();
    }

    public function delivery_note()
    {
        return $this->morphOne(ConsultationNote::class, 'visit')->latest()->where('code', NoteCodes::Delivery);
    }

    #[\Override]
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [Status::closed->value, Status::cancelled->value]);
    }
}
