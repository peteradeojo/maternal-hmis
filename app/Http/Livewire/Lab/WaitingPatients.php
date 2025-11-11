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

    // public $documentations = [];
    public $visits = [];

    public function mount()
    {
        $this->load();
    }

    public function load()
    {
        // $this->documentations = DocumentationTest::whereHasMorph('testable', [
        //     Visit::class,
        //     AdmissionPlan::class,
        //     Admission::class,
        //     GeneralVisit::class,
        //     AncVisit::class,
        //     AntenatalProfile::class,
        //     Documentation::class,
        // ])->selectRaw(
        //     'patient_id, testable_type, testable_id, MIN(created_at) as created_at'
        // )->groupBy(
        //     'patient_id',
        //     'testable_type',
        //     'testable_id'
        // )->whereIn('status', [
        //     Status::pending->value,
        //     Status::active->value,
        // ])->orderBy('created_at', 'DESC')->get()->load('testable', 'patient');
        $this->visits = Visit::has('tests')->orWhereHas('visit', function ($query) {
            $query->has('tests');
        })->latest()->get();
    }

    public function render()
    {
        return view('livewire.lab.waiting-patients');
    }
}
