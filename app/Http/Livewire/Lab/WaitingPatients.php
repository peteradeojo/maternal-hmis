<?php

namespace App\Http\Livewire\Lab;

use App\Models\User;
use App\Models\Visit;
use Livewire\Component;

class WaitingPatients extends Component
{
    public User $user;

    public $visits;

    public function mount() {}

    public function load()
    {
        $query = Visit::has('tests')->orWhereHas('visit', function ($query) {
            $query->has('tests');
        });

        $this->visits = $query->latest()->get();
    }

    public function render()
    {
        return view('livewire.lab.waiting-patients');
    }
}
