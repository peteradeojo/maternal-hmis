<?php

namespace App\Models;

use App\Interfaces\OperationalEvent;
use App\Traits\Documentable;
use App\Traits\HasVisitData;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class AdmissionPlan extends Model implements OperationalEvent
{
    use HasFactory, HasVisitData, Documentable;

    protected $fillable  = [
        'admission_id',
        'user_id', 'indication', 'note'
    ];

    public function admission() {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    final public function treatments() {
        return $this->morphMany(DocumentationPrescription::class, 'event');//->where('status', Status::active->value);
    }

    #[Override]
    public function tests() {
        return $this->morphMany(DocumentationTest::class, 'testable')->latest();
    }

    public function scans() {
        return $this->morphMany(PatientImaging::class, 'documentable');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function notes() {
        return $this->morphMany(ConsultationNote::class, 'visit');
    }

    public function patient(): Attribute {
        return Attribute::make(
            get: fn($_, $attributes) => Admission::find($attributes['admission_id'])?->patient,
        );
    }
}
