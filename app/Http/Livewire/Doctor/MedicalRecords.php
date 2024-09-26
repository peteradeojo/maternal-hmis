<?php

namespace App\Http\Livewire\Doctor;

use App\Enums\Department;
use App\Enums\Status;
use App\Models\AncVisit;
use App\Models\Department as ModelsDepartment;
use App\Models\GeneralVisit;
use App\Models\Visit;
use App\Notifications\StaffNotification;
use Livewire\Component;

class MedicalRecords extends Component
{
    public GeneralVisit|AncVisit $visit;

    public function mount($visit)
    {
        $this->visit = $visit->load(['prescriptions', 'tests', 'diagnoses']);
    }

    public function hydrate()
    {
        $this->visit->refresh();
    }

    public function render()
    {
        return view('livewire.doctor.medical-records');
    }

    public function close()
    {
        $this->visit->refresh();

        $notify = [];

        $returning = false;

        if ($this->visit->imagings->count()  > 0) {
            $this->visit->awaiting_radiology = true;
            $notify[] = Department::RAD->value;
            $returning = true;
        }

        if ($this->visit->tests->count()  > 0) {
            $this->visit->awaiting_lab_results = true;
            $notify[] = Department::LAB->value;
            $returning = true;
        }
        if ($this->visit->prescriptions->count()  > 0) {
            $this->visit->awaiting_pharmacy = true;
            $notify[] = Department::PHA->value;
        }

        if ($returning) {
            $this->visit->status = Status::active->value;
            $this->visit->awaiting_doctor = true;
        } else {
            $this->visit->status = Status::completed->value;
            $this->visit->awaiting_doctor = false;
        }

        $this->visit->saveOrFail();

        foreach ($notify as $department) {
            ModelsDepartment::where('id', $department)->first()?->notifyParticipants(new StaffNotification("You have a new request for {$this->visit->patient->name}"));
        }

        $this->redirect('/dashboard');
    }
}
