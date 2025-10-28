<?php

namespace App\Http\Livewire;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class LmpForm extends Component
{
    public $edd;
    public $lmp;

    public function mount($profile)
    {
        $this->edd = $profile->edd?->format('Y-m-d');
        $this->lmp = $profile->lmp?->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.lmp-form');
    }

    public function setLMP($value)
    {
        $this->lmp = $value;
        $this->calculateEDD();
    }

    public function setEDD($value) {
        $this->edd = $value;
        $this->calculateLmp();
    }

    public function calculateEDD()
    {
        $this->edd = Carbon::parse($this->lmp)?->addMonths(9)->addDays(7)->format('Y-m-d');
    }

    public function calculateLmp() {
        $this->lmp = Carbon::parse($this->lmp)?->subMonths(9)->subDays(7)->format('Y-m-d');
    }

    public function clear()
    {
        $this->lmp = null;
        $this->edd = null;
    }
}
