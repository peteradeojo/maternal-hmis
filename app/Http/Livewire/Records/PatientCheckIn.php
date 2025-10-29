<?php

namespace App\Http\Livewire\Records;

use App\Enums\Department;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PatientCheckIn extends Component
{
    public $patient;
    public $consultants;

    #[Validate('required|integer')]
    public $consultant;

    #[Validate('required|integer')]
    public $visit_type;

    public function mount()
    {
        // if ($this->patient->category->name == "Antenatal") {
        //     $this->visit_type = "2";
        // }

        $this->consultants = User::whereIn(
            'department_id',
            [
                Department::DOC->value,
            ]
        )->get();

        // dd($this->consultants);
    }

    public function render()
    {
        return view('livewire.records.patient-check-in');
    }

    public function startVisit()
    {
        // $this->validate();
        dd($this->consultant, $this->visit_type);
    }
}
