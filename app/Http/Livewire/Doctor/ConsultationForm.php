<?php

namespace App\Http\Livewire\Doctor;

use App\Models\AncVisit;
use App\Models\Visit;
use Livewire\Component;

class ConsultationForm extends Component
{
    public Visit $visit;
    public array $complaints;
    public array $tests;
    public array $prescriptions;
    public array $diagnoses;

    public function render()
    {
        if ($this->visit->visit instanceof AncVisit) {
            return view('doctors.anc-visit-form', [
                'ancVisit' => $this->visit->visit,
                'visit_id' => $this->visit->id
            ]);
        }

        return view('doctors.basic-consultation-form', ['visit' => $this->visit]);
    }
}
