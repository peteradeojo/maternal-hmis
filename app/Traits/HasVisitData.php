<?php

namespace App\Traits;

use App\Models\Patient;
use App\Models\Product;
use App\Dto\PrescriptionDto;
use App\Models\ConsultationNote;
use App\Models\PatientExaminations;
use App\Models\PatientHistory;
use App\Models\PatientImaging;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasVisitData
{
    public function notes()
    {
        return $this->morphMany(ConsultationNote::class, 'visit');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function addPrescription(Patient $patient, Product $product, PrescriptionDto $data, $event)
    {
        return $this->prescriptions()->create([
            'patient_id' => $patient->id,
            'prescriptionable_type' => $product::class,
            'prescriptionable_id' => $product->id,
            'name' => $product->name,
            'dosage' => $data->dosage,
            'duration' => $data->duration,
            'route' => $data->route,
            'frequency' => $data->frequency,
            'requested_by' => auth()->user()?->id,
            'event_type' => $event::class,
            'event_id' => $event->id,
        ]);
    }

    public function visit()
    {
        return $this->morphOne(Visit::class, 'visit');
    }

    public function histories()
    {
        return $this->morphMany(PatientHistory::class, 'visit');
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
}
