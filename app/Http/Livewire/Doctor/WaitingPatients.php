<?php

namespace App\Http\Livewire\Doctor;

use App\Models\User;
use App\Models\Visit;
use Livewire\Component;

class WaitingPatients extends Component
{
    public User $user;
    public $visits = [];

    public function mount()
    {
        $this->load();
    }

    public function load()
    {
        $this->visits = Visit::whereNotNull('vitals')->where('awaiting_doctor', true)->get();
    }

    public function render()
    {
        return view('livewire.doctor.waiting-patients');
    }
}
