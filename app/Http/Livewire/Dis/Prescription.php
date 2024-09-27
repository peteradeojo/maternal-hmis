<?php

namespace App\Http\Livewire\Dis;

use App\Models\AncVisit;
use App\Models\Documentation;
use App\Models\GeneralVisit;
use Livewire\Component;

class Prescription extends Component
{
    public $doc;

    public function mount($doc) {
        $this->doc = $doc;
    }

    public function render()
    {
        return view('livewire.dis.prescription');
    }
}
