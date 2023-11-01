<?php

namespace App\Traits;

use App\Models\PatientImaging;
use App\Models\DocumentationTest;
use App\Models\DocumentedDiagnosis;
use App\Models\DocumentationComplaints;
use App\Models\DocumentationPrescription;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Documentable
{
    public function radios()
    {
        return $this->morphMany(PatientImaging::class, 'documentable');
    }

    public function treatments()
    {
        return $this->morphMany(DocumentationPrescription::class, 'prescriptionable')->latest();
    }

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
        return $this->morphMany(DocumentationTest::class, 'testable')->latest();
    }
}
