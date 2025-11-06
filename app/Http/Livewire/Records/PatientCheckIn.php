<?php

namespace App\Http\Livewire\Records;

use App\Enums\AppNotifications;
use App\Enums\Department;
use App\Enums\Status;
use App\Events\NotificationSent;
use App\Models\AncVisit;
use App\Models\GeneralVisit;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PatientCheckIn extends Component
{
    public $patient;
    public $consultants;

    #[Validate('required|integer')]
    public $consultant;

    #[Validate('required|integer|in:1,2')]
    public $visit_type;

    private $user;

    public function mount()
    {
        if ($this->patient->category->name == "Antenatal") {
            $this->visit_type = 2;
        } else {
            $this->visit_type = 1;
        }

        $this->consultants = User::whereIn(
            'department_id',
            [
                Department::DOC->value,
            ]
        )->get();

        $this->consultant = $this->consultants->first()?->id;
    }

    public function render()
    {
        return view('livewire.records.patient-check-in');
    }

    public function startVisit()
    {
        $this->validate();

        if ($this->patient->visits->count() > 0 && $this->patient->visits[0]?->status == Status::active->value) {
            notifyUserError("Last visit for {$this->patient->card_number} is still active.", auth()->user(), ['mode' => AppNotifications::$IN_APP]);
            return;
        }

        $subVisit = match ((string) $this->visit_type) {
            "1" => GeneralVisit::class,
            "2" => AncVisit::class,
        };
        $subVisit = new $subVisit([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->consultant,
        ]);

        if (is_a($subVisit, AncVisit::class)) {
            if ($this->patient->anc_profile->status != Status::active->value) {
                notifyUserError("Patient does not have an active antenatal profile. Please create one.", auth()->user(), ['mode' => AppNotifications::$IN_APP]);
                return;
            }

            $subVisit->antenatal_profile_id = $this->patient->anc_profile?->id;
        }
        $subVisit->save();

        $visit = new Visit([
            'patient_id' => $this->patient->id,
            'consultant_id' => $this->consultant,
            'visit_type' => $subVisit::class,
            'visit_id' => $subVisit->id,
            'awaiting_vitals' => 1,
            'awaiting_doctor' => 1,
            'status' => Status::active->value,
        ]);

        $visit->save();

        notifyUserSuccess("Consultation started for {$this->patient->card_number}", auth()->user(), [
            'mode' => AppNotifications::$IN_APP,
            'close_modal' => true
        ]);

        notifyDepartment(Department::NUR->value, [
            'message' => "Consultation started for {$this->patient->card_number}",
            'bg' => ['bg-green-400', 'text-white'],
        ], [
            'mode' => AppNotifications::$DESKTOP,
        ]);

        notifyDepartment(Department::DOC->value, [
            'message' => "Consultation started for {$this->patient->card_number}",
            'bg' => ['bg-green-400', 'text-white'],
        ], [
            'mode' => AppNotifications::$DESKTOP,
        ]);
    }
}
