<?php

namespace App\Http\Livewire\Doctor;

use App\Models\User;
use Livewire\Component;

class AntenatalHistory extends Component
{
    public User $user;
    public $history = [];

    public function mount(User $user)
    {
        $this->user = $user;
    }

    protected function load()
    {
        $this->history = $this->user->patient->antenatalProfile[0]->history;
    }

    public function render()
    {
        return view('livewire.doctor.antenatal-history');
    }
}
