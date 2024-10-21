<?php

namespace App\Http\Livewire\Admissions;

use Livewire\Component;

class Plan extends Component
{
    public $visit;
    public $plans = [];

    public function render()
    {
        return view('livewire.admissions.plan');
    }


    public function addPrescription($data)
    {
        $this->plans[] = $data;
    }

    public function removePlanItem($id)
    {
        $this->plans = array_slice($this->plans, 0, $id) + array_slice($this->plans, $id + 1, count($this->plans));
    }

    public function savePlan() {}
}
