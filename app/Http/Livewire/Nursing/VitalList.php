<?php

namespace App\Http\Livewire\Nursing;

use App\Enums\Status;
use App\Models\Visit;
use Livewire\Component;

class VitalList extends Component
{
    public $visits = [];

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->visits = Visit::where('status', Status::active->value)->where(function ($query) {
            $query->whereHas('visit', function ($query) {
                $query->doesntHave('vitals');
            });
        })->get();
    }

    public function render()
    {
        return view('livewire.nursing.vital-list');
    }

    public function refreshData()
    {
        $this->fetchData();
        $this->emit('dataUpdated');
    }
}
