<?php

namespace App\Http\Livewire\Dashboard;

use App\Enums\Status;
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

        $this->visits = Visit::latest()->where('status', '!=', Status::completed->value)->awaiting()->limit(50)->get();
    }

    public function hydrate()
    {
        $this->getData();
        $this->dispatchBrowserEvent('reinitialize-datatable');
    }

    public function render()
    {
        return view('livewire.dashboard.patient-stats');
    }

    public function updated() {
        $this->dispatchBrowserEvent('reinitialize-datatable');
    }
}
