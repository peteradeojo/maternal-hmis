<?php

namespace App\Traits;

use App\Models\Patient;
use App\Models\Product;
use App\Interfaces\OperationalEvent;
use App\Models\ConsultationNote;
use App\Models\PatientHistory;
use App\Models\Visit;
use App\Models\Vitals;
use Illuminate\Database\Eloquent\Casts\Attribute;
use stdClass;

trait HasVisitData
{
    public function notes()
    {
        return $this->morphMany(ConsultationNote::class, 'visit')->latest();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function addPrescription(Patient $patient, $product, mixed $data, OperationalEvent $event)
    {
        if ($product instanceof stdClass) { // product was newly created triggers this behavior
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

    public function vitals()
    {
        return $this->morphOne(Vitals::class, 'recordable')->latest();
    }

    public function svitals()
    {
        return $this->morphMany(Vitals::class, 'recordable')->latest();
    }

    public function firstVisit(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attrs) {
                return static::where('patient_id', $attrs['patient_id'])->where('id', '<', $attrs['id'])->count() < 1;
            }
        );
    }

    public function getTestResult($name) {
        $test = $this->tests->where('name', $name)->first();
        if (empty($test) || empty($test->results)) {
            return '';
        }

        return $test->results[0]->result;
    }

    public function getTestResults($name, $key = null) {
        $test = $this->tests->filter(fn ($n) => strtolower($n->name) == strtolower($name))->first();

        if (!$test) return "Not requested.";

        if (!empty($key)) {
            foreach($test->results ?? [] as $o) {
                if (strtolower(@$o->description) == strtolower($key)) return @$o->result;
            }
            return "No result.";
        }

        return $test->results;
    }
}
