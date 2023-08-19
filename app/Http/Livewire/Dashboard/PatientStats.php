<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;

class PatientStats extends Component
{
    public User $user;

    public $patients = 0;
    public $patientsToday = 0;
    public $currentAdmissions = 0;

    public function render()
    {
        return view('livewire.dashboard.patient-stats');
    }
}
