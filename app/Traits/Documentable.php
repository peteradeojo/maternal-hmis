<?php

namespace App\Traits;

use App\Enums\Status;
use App\Models\PatientImaging;
use App\Models\DocumentationTest;
use App\Models\DocumentedDiagnosis;
use App\Models\DocumentationComplaints;
use App\Models\DocumentationPrescription;
use App\Models\PatientExaminations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Documentable
{
    public function complaints()
    {
        return $this->morphMany(DocumentationComplaints::class, 'documentable');
    }

    public function diagnoses()
    {
        return $this->morphMany(DocumentedDiagnosis::class, 'diagnosable');
    }

    public function tests(): MorphMany
    {
        return $this->morphMany(DocumentationTest::class, 'testable');
    }

    public function valid_tests(): MorphMany
    {
        return $this->morphMany(DocumentationTest::class, 'testable')->where('status', '!=', Status::cancelled->value);
    }

    public function allPrescriptionsAvailable(): Attribute
    {
        return Attribute::make(get: fn () => $this->treatments->every(fn ($t) => $t->status == Status::quoted->value));
    }

    public  function examination(): MorphOne
    {
        return $this->morphOne(PatientExaminations::class, 'visit');
    }

    public function imagings()
    {
        return $this->morphMany(PatientImaging::class, 'documentable');
    }

    public function radios()
    {
        return $this->imagings();
    }

    public function prescriptions() {
        return $this->treatments();
    }

    public function treatments()
    {
        return $this->morphMany(DocumentationPrescription::class, 'event');
    }
}
