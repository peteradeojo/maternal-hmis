<?php

namespace App\Http\Livewire\Nursing;

use Livewire\Component;

class VitalList extends Component
{
    public $patients = [];

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData() {
        $this->patients = [
            collect([
                'name' => fake()->name,
                'card_number' => fake()->creditCardNumber,
                'category' => 'OPD',
            ]),
            collect([
                'name' => fake()->name,
                'card_number' => fake()->creditCardNumber,
                'category' => 'IPD',
            ]),
        ];
    }

    public function render()
    {
        return view('livewire.nursing.vital-list', ['patients' => $this->patients]);
    }

    public function refreshData() {
        $this->fetchData();
        $this->emit('dataUpdated');
    }
}
