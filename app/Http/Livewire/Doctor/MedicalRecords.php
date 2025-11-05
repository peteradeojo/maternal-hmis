<?php

namespace App\Http\Livewire\Doctor;

use App\Enums\AppNotifications;
use App\Enums\Department;
use App\Enums\Status;
use App\Models\Visit;
use Livewire\Component;

class MedicalRecords extends Component
{
    public Visit $visit;

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

    public function deleteHistory($id)
    {
        $this->visit->histories()->where('id', $id)->delete();
        $this->visit->refresh();
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
            notifyDepartment($department, [
                'title' => 'New Request',
                'message' => "You have a new request for {$this->visit->patient->name}",
            ], [
                'mode' => AppNotifications::$BOTH,
            ]);
        }

        $this->redirect('/dashboard');
    }

    public function removeTest($id) {
        $this->visit->tests()->where('id', $id)->delete();
        $this->dispatch('$refresh');
    }
}
