<?php

namespace App\Http\Livewire\Lab;

use App\Enums\Status;
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
        $this->documentations = Visit::whereHas('visit', function ($query) {
            $query->whereHas('tests', function ($q) {
                $q->where('status', '!=', Status::completed->value);
            });
        })->limit(100)->get();
    }

    public function render()
    {
        return view('livewire.lab.waiting-patients');
    }
}
