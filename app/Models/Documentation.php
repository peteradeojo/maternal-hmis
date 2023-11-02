<?php

namespace App\Models;

use App\Enums\Status;
use App\Models\PatientImaging;
use App\Interfaces\Documentable;
use App\Traits\Documentable as TraitsDocumentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Documentation extends Model implements Documentable
{
    use HasFactory, TraitsDocumentable;

    protected $fillable = [
        'visit_id',
        'patient_id',
        'user_id',
        'symptoms',
        'complaints_history',
        'prognosis',
        'comment',
        'status',
        'complaints_durations',
    ];

    protected $with = ['patient', 'tests'];

    protected $appends = ['all_tests_completed'];

    protected $casts = [
        // 'symptoms' => 'array'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function exams()
    {
        return $this->hasMany(PatientExaminations::class, 'documentation_id');
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
