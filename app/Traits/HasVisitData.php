<?php

namespace App\Traits;

use App\Models\Patient;
use App\Models\Product;
use App\Dto\PrescriptionDto;
use App\Models\ConsultationNote;
use App\Models\DocumentationPrescription;
use App\Models\PatientExaminations;
use App\Models\PatientHistory;
use App\Models\PatientImaging;
use App\Models\Visit;
use App\Models\Vitals;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use stdClass;

trait HasVisitData
{
    public function notes()
    {
        return $this->morphMany(ConsultationNote::class, 'visit');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function addPrescription(Patient $patient, $product, PrescriptionDto $data, $event)
    {
        if ($product instanceof stdClass) {
            $product = new Product((array) $product);
            $product->save();
        }

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

    public function prescriptions() {
        return $this->treatments();
    }

    public function treatments()
    {
        return $this->morphMany(DocumentationPrescription::class, 'event');
    }

    public function vitals() {
        return $this->morphOne(Vitals::class, 'recordable')->latest();
    }
}
