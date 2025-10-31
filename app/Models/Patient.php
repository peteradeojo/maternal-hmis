<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Religion;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'card_number',
        'name',
        'gender',
        'phone',
        'email',
        'address',
        'dob',
        'marital_status',
        'occupation',
        'religion',
        'tribe',
        'place_of_origin',
        'nok_name',
        'nok_phone',
        'nok_address',
        'spouse_name',
        'spouse_phone',
        'spouse_occupation',
        'spouse_educational_status',
        'category_id'
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    protected $appends = ['gender_value'];

    protected static function booted()
    {
        static::creating(function ($patient) {
            if ($patient->card_number === null)
                $patient->card_number = static::generateCardNumber($patient->category);
        });
    }

    public function getAncProfileAttribute()
    {
        return $this->antenatalProfiles->count() > 0 ? $this->antenatalProfiles[0] : null;
    }

    protected function maritalstatus(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value != 0 ? MaritalStatus::tryFrom($value)?->name ?? "Unknown" : "Unknown",
        );
    }

    protected function religion(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value != 0 ? Religion::tryFrom($value)?->name ?? "Unknown" : "Unknown",
        );
    }

    protected function gender(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return Gender::tryFrom($value)?->name ?? "Unknown";
            },
        );
    }

    public static function generateCardNumber($category)
    {
        $prefix = Patient::where('category_id', $category)->count();
        $prefix = str_pad($prefix, 3, '0', STR_PAD_LEFT);
        return $prefix . date('my');
    }

    public function category()
    {
        return $this->belongsTo(PatientCategory::class, 'category_id');
    }

    public function getGenderValueAttribute()
    {
        return $this->gender; // Gender::tryFrom($this->gender)?->name;
    }

    public function antenatalProfiles()
    {
        return $this->hasMany(AntenatalProfile::class, 'patient_id')->latest();
    }

    public function tests()
    {
        return $this->hasMany(DocumentationTest::class, 'patient_id')->latest();
    }
    public function prescriptions()
    {
        return $this->hasMany(DocumentationPrescription::class)->latest();
    }
    public function documentations()
    {
        return $this->hasMany(Documentation::class)->limit(10)->latest();
    }

    public function insurance()
    {
        return $this->hasMany(InsuranceProfiles::class, 'patient_id');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'patient_id')->latest();
    }

    public function notes()
    {
        return $this->hasMany(ConsultationNote::class, 'patient_id');
    }

    public function scans()
    {
        return $this->hasMany(PatientImaging::class, 'patient_id');
    }

    public function scopeActiveInsurance()
    {
        $this->insurance()->where('status', Status::active);
    }
}
