<?php

namespace App\Http\Livewire\Admissions;

use Livewire\Component;

class Create extends Component
{
    public $visit;

    public function render()
    {
        return view('livewire.admissions.create');
    }
}
