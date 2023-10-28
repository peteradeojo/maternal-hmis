<?php

namespace App\Models;

use App\Enums\Status;
use App\Interfaces\Documentable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Documentation extends Model implements Documentable
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'patient_id',
        'user_id',
        'symptoms',
        'complaints_history',
        'prognosis',
        'comment',
        'status',
    ];

    protected $with = ['patient', 'tests'];

    protected $appends = ['all_tests_completed'];

    protected $casts = [
        'symptoms' => 'array'
    ];

    public function tests(): MorphMany
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

    public function complaints()
    {
        return $this->hasMany(DocumentationComplaints::class, 'documentation_id');
    }

    public function exams()
    {
        return $this->hasMany(PatientExaminations::class, 'documentation_id');
    }

    public function radios()
    {
        return $this->hasMany(PatientImaging::class, 'documentation_id');
    }

    public function diagnoses() {
        return $this->morphMany(DocumentedDiagnosis::class, 'diagnosable');
    }

    public function allTestsCompleted(): Attribute
    {
        return Attribute::make(get: fn () => $this->tests->every(fn ($test) => $test->status === Status::completed->value));
    }

    public function allPrescriptionsAvailable(): Attribute
    {
        return Attribute::make(get: fn () => $this->treatments->every(fn ($t) => $t->status == Status::quoted->value));
    }
}
