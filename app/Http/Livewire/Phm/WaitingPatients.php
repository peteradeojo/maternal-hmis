<?php

namespace App\Http\Livewire\Phm;

use App\Models\Documentation;
use App\Models\DocumentationPrescription;
use Livewire\Component;

class WaitingPatients extends Component
{
    public $data = [];
    public function mount()
    {
        $this->data = Documentation::with(['patient'])->has('treatments')->get();
    }

    public function render()
    {
        return view('livewire.phm.waiting-patients');
    }
}
