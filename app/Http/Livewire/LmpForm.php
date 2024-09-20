<?php

namespace App\Http\Livewire;

use Illuminate\Support\Carbon;
use Livewire\Component;

class LmpForm extends Component
{
    public $edd;
    public $lmp;

    public function render()
    {
        return view('livewire.lmp-form');
    }

    public function setLMP($value)
    {
        $this->lmp = $value;
        $this->calculateEDD();
    }

    public function calculateEDD()
    {
        $this->edd = Carbon::parse($this->lmp)?->addMonths(9)->addDays(7)->format('Y-m-d');
    }

    public function clear() {
        $this->lmp = null;
        $this->edd = null;
    }
}
