<?php

namespace App\Http\Livewire\Rad;

use App\Models\Patient;
use App\Models\Visit;
use Livewire\Component;

class WaitingPatients extends Component
{
    public $waitlist;

    public function render()
    {
        return view('livewire.rad.waiting-patients');
    }

    public function mount()
    {
        $this->load();
    }

    protected function load()
    {
        $this->waitlist = Visit::with(['patient'])->whereHas('visit', function ($query) {
            $query->whereHas('radios', function ($query) {
                $query->where('results', null);
            });
        })->latest()->get();
    }
}
