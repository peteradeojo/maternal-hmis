<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'card_number', 'name', 'gender', 'phone', 'email', 'address', 'dob', 'marital_status',
        'occupation', 'religion', 'tribe', 'place_of_origin', 'nok_name', 'nok_phone',
        'nok_address', 'spouse_name', 'spouse_phone', 'spouse_occupation', 'spouse_educational_status', 'category_id'
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
        return Gender::tryFrom($this->gender)?->name;
    }

    public function antenatalProfiles()
    {
        return $this->hasMany(AntenatalProfile::class)->where('status', Status::active->value)->latest();
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
        return $this->hasMany(Documentation::class)->latest();
    }
}
