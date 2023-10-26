<?php

namespace App\Services;

use App\Enums\Department as EnumsDepartment;
use App\Models\Department;
use App\Models\Patient;
use App\Notifications\StaffNotification;

class PatientService
{
    public function createInsuranceProfile(Patient $patient, $data)
    {
        $profile = $patient->insurance()->create($data);

        Department::find(EnumsDepartment::NHI->value)?->notifyParticipants(new StaffNotification("New Registration: {$patient->name}"));
    }

    public function registerPatient($data)
    {
    }
}
