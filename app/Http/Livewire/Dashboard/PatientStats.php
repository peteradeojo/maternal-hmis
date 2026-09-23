<?php

namespace App\Http\Livewire\Dashboard;

use App\Enums\Status;
use App\Models\Admission;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Services\LocationContext;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class PatientStats extends Component
{
    public User $user;

    public $patients = 0;
    public $patientsToday = 0;
    public $currentAdmissions = 0;
    public $visits = [];
    public $todayVisits = 0;

    public $stats = [];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->getData();
    }

    private function getData()
    {
        $location = app(LocationContext::class)->id();
        $data = Cache::get("dashboard-stats:{$location}");

        if (!$data) {
            $this->patients = Patient::count();
            $this->patientsToday = Patient::whereDate('created_at', today())->count();
            $this->todayVisits = Visit::where('location_id', $location)->whereDate('created_at', today())->count();

            $this->currentAdmissions = Admission::active()->count();

            $this->stats['pendingBills'] = Bill::where('status', Status::pending->value)->count();

            Cache::set("dashboard-stats:{$location}", [
                'patients' => $this->patients,
                'patientsToday' => $this->patientsToday,
                'todayVisits' => $this->todayVisits,
                'currentAdmissions' => $this->currentAdmissions,
                'stats' => $this->stats,
            ], now()->addMinutes(5));
        } else {
            $this->patientsToday = $data['patientsToday'];
            $this->patients = $data['patients'];
            $this->currentAdmissions = $data['currentAdmissions'];
            $this->todayVisits = $data['todayVisits'];
            $this->stats = $data['stats'];
        }

        $this->visits = Visit::where("status", "=", Status::active->value)->latest()->limit(50)->get();
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

    public function updated()
    {
        $this->dispatchBrowserEvent('reinitialize-datatable');
    }
}
