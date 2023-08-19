<?php

namespace App\Http\Livewire\Doctor;

use App\Models\User;
use Livewire\Component;

class WaitingPatients extends Component
{
    public User $user;
    public $patients = [];

    public function mount()
    {
        $this->patients = [
            collect([
                'name' => fake()->name,
                'card_number' => fake()->creditCardNumber,
                'category' => 'OPD',
            ])
        ];
    }

    public function render()
    {
        return view('livewire.doctor.waiting-patients');
    }
}
