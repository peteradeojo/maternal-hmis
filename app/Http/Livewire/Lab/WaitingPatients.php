<?php

namespace App\Http\Livewire\Lab;

use App\Enums\Status;
use App\Models\Documentation;
use App\Models\DocumentationTest;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
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
        // $this->documentations = Documentation::whereHas('tests', function ($q) {
        //     $q->where('status', '!=', Status::completed->value);
        // })->limit(100)->get();
        $this->documentations = Visit::whereHas('tests', function ($q) {
            $q->where('status', '!=', Status::completed->value);
        })->limit(100)->get();
    }

    public function render()
    {
        return view('livewire.lab.waiting-patients');
    }
}
