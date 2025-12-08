<?php

namespace App\Http\Livewire\Billing;

use App\Services\TreatmentService;
use Livewire\Component;

class AddPrescription extends Component
{
    public $selection;
    public $count = 0;

    public function render()
    {
        return view('livewire.billing.add-prescription');
    }

    public function addDrug($data)
    {
        $this->selection = [...$data, 'dosage' => null, 'frequency' => 'stat', 'duration' => null,];
    }

    public function saveRequest()
    {
        $details = [
            'product' => (object) $this->selection, //['product'],
            'data' => (object) [
                'name' => $this->selection['name'],
                'dosage' => $this->selection['dosage'],
                'frequency' => $this->selection['frequency'],
                'duration' => $this->selection['duration'],
            ],
        ];

        $this->dispatch("selected", ...$details);
        $this->cancel();
    }

    public function cancel()
    {
        $this->reset("selection");
    }

    public function getCount()
    {
        $this->count = TreatmentService::getCount($this->selection, (object) [
            'name' => $this->selection['name'],
            'dosage' => $this->selection['dosage'],
            'frequency' => $this->selection['frequency'],
            'duration' => $this->selection['duration'],
        ]);
    }
}
