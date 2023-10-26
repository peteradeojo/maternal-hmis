<?php

namespace App\Http\Livewire\Dis;

use App\Models\Documentation;
use Livewire\Component;

class Prescription extends Component
{
    public Documentation $doc;

    public function mount(Documentation $doc) {
        $this->doc = $doc;
    }

    public function render()
    {
        return view('livewire.dis.prescription');
    }
}
