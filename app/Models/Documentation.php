<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'patient_id',
        'user_id',
        'symptoms',
        'prognosis',
        'comment',
        'status',
    ];

    protected $with = ['patient', 'tests'];

    protected $appends = ['all_tests_completed'];

    public function tests()
    {
        return $this->morphMany(DocumentationTest::class, 'testable')->latest();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function treatments()
    {
        return $this->morphMany(DocumentationPrescription::class, 'prescriptionable')->latest();
    }

    public function allTestsCompleted(): Attribute
    {
        return Attribute::make(get: fn () => $this->tests->every(fn ($test) => $test->status === Status::completed->value));
    }
}
