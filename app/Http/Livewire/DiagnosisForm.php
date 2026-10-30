<?php

namespace App\Http\Livewire;

use App\Models\IcdDiagnosis;
use App\Models\Visit;
use Livewire\Component;

class DiagnosisForm extends Component
{
    public Visit $visit;
    public $results = [];
    public string $query = "";

    public function mount(Visit $event)
    {
        $this->visit = $event;
    }

    public function updatedQuery()
    {
        if (strlen($this->query) >= 4) {
            $this->results = IcdDiagnosis::where(function ($query) {
                $query->where('short_description', 'ilike', "%$this->query%")->orWhere('code', 'ilike', "$this->query%");
            })->get();
        } else {
            $this->results = [];
        }
    }

    public function addDiagnosis($id)
    {
        try {

            $this->visit->diagnoses()->create([
                'icd_id' => $id,
                'user_id' => request()->user()->id,
                'patient_id' => $this->visit->patient_id,
            ]);

            notifyUserSuccess("Diagnosis saved!", request()->user()->id);
            $this->reset(['results']);
            $this->visit->refresh();
        } catch (\Exception $e) {
            // dump($e);
            notifyUserError("Unable to save diagnosis", request()->user()->id);
        }
    }

    public function render()
    {
        return view('livewire.diagnosis-form');
    }
}
