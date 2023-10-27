<?php

namespace App\Http\Livewire;

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
    }

    public function calculateEDD()
    {
        $this->edd = date('Y-m-d', strtotime($this->lmp . ' + 9 months + 7 days'));
    }

    public function clear() {
        $this->lmp = null;
        $this->edd = null;
    }
}
