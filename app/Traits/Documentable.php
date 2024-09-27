<?php

namespace App\Traits;

use App\Enums\Status;
use App\Models\PatientImaging;
use App\Models\DocumentationTest;
use App\Models\DocumentedDiagnosis;
use App\Models\DocumentationComplaints;
use App\Models\DocumentationPrescription;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    public function allPrescriptionsAvailable(): Attribute
    {
        return Attribute::make(get: fn () => $this->treatments->every(fn ($t) => $t->status == Status::quoted->value));
    }
}
