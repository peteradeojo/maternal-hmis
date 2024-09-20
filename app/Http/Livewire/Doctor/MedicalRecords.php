<?php

namespace App\Http\Livewire\Doctor;

use App\Models\Visit;
use Livewire\Component;

class MedicalRecords extends Component
{
    public Visit $visit;

    public function mount(Visit $visit)
    {
        $this->visit = $visit->load(['prescriptions', 'tests', 'diagnoses']);
    }

    public function hydrate()
    {
        $this->visit->refresh();
    }

    public function render()
    {
        return view('livewire.doctor.medical-records');
    }
}
