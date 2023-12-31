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
        'fundal_height',
        'presentation',
        'lie',
        'fetal_heart_rate',
        'presentation_relationship',
        'status',
    ];

    protected $casts = [
        'lmp' => 'datetime',
        'edd' => 'datetime',
        'tests' => 'array',
        'vitals' => 'array'
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

    public function tests() {
        return $this->morphMany(DocumentationTest::class, 'testable');
    }

    public function getVitals() {
        return $this->vitals;
    }
}
