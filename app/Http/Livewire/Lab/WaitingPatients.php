<?php

namespace App\Http\Livewire\Lab;

use App\Enums\Status;
use App\Models\Admission;
use App\Models\AdmissionPlan;
use App\Models\AncVisit;
use App\Models\AntenatalProfile;
use App\Models\Documentation;
use App\Models\DocumentationTest;
use App\Models\GeneralVisit;
use App\Models\User;
use App\Models\Visit;
use Livewire\Component;

class WaitingPatients extends Component
{
    public User $user;

    public $visits;

    public function mount()
    {
    }

    public function load()
    {
        $this->visits = Visit::has('tests')->orWhereHas('visit', function ($query) {
            $query->has('tests');
        })->latest()->get();
    }

    public function render()
    {
        return view('livewire.lab.waiting-patients');
    }
}
