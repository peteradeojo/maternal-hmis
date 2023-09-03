<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Livewire\Component;

class PatientStats extends Component
{
    public User $user;

    public $patients = 0;
    public $patientsToday = 0;
    public $currentAdmissions = 0;
    public $visits = [];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->getData();
    }

    private function getData()
    {
        $this->patients = Patient::count();
        $this->patientsToday = Patient::whereDate('created_at', today())->count();

        $this->visits = Visit::latest()->limit(50)
            ->get();
    }

    public function hydrate()
    {
        $this->getData();
    }

    public function render()
    {
        return view('livewire.dashboard.patient-stats');
    }
}
