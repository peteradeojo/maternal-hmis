<?php

namespace App\Models;

use App\Enums\AncCategory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntenatalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'lmp',
        'edd',
        'spouse_name',
        'spouse_phone',
        'spouse_occupation',
        'spouse_educational_status',
        'gravida',
        'parity',
        'card_type',
        'status',
        'blood_pressure',
        'weight',
        'height',
        'pulse',
        'respiratory_rate',
        'temperature',
        'doctor_id',
        'fundal_height',
        'presentation',
        'lie',
        'fetal_heart_rate',
        'presentation_relationship',
    ];

    protected $casts = [
        'tests' => 'array'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function history()
    {
        return $this->hasMany(AncVisit::class)->where('doctor_id', '!=', null);
    }

    public function cardType(): Attribute
    {
        return Attribute::make(get: fn ($value) => AncCategory::tryFrom($value)->name);
    }
}
