<?php

namespace App\Http\Livewire\Nursing;

use App\Enums\Status;
use App\Models\Visit;
use App\Models\Vitals;
use Livewire\Component;

class VitalList extends Component
{
    public $visits = 0;

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->visits = Vitals::getPendingVitalVisits()->count();
    }

    public function render()
    {
        return view('livewire.nursing.vital-list');
    }

    public function refreshData()
    {
        $this->fetchData();
        // $this->emit('dataUpdated');
        // $thi
    }
}
