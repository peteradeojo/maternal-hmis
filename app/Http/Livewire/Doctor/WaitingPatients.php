<?php

namespace App\Http\Livewire\Doctor;

use App\Enums\Status;
use App\Models\User;
use App\Models\Visit;
use Livewire\Component;

class WaitingPatients extends Component
{
    public User $user;
    // public $visits = [];

    public function mount()
    {
        // $this->load();
    }

    public function load()
    {
        // $this->visits = Visit::where(function ($query) {
        //     $query->where('awaiting_doctor', true);
        // })->where('status', Status::active->value)->latest()->get();
    }

    public function render()
    {
        return view('livewire.doctor.waiting-patients');
    }
}
