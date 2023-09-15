<?php

namespace App\Http\Livewire\Lab;

use App\Models\Documentation;
use App\Models\DocumentationTest;
use App\Models\Patient;
use App\Models\User;
use Livewire\Component;

class WaitingPatients extends Component
{
    public User $user;

    public $documentations = [];

    public function mount()
    {
        $this->load();
    }

    public function load()
    {
        $this->documentations = Documentation::whereHas('tests', function ($q) {
        })->whereHas('visit', function ($query) {
            $query->where('awaiting_lab_results', true); //->orWhere('awaiting_tests', true);
        })->limit(100)->get();
    }

    public function render()
    {
        return view('livewire.lab.waiting-patients');
    }
}
