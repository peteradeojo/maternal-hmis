<?php

namespace App\Services;

use App\Enums\AppNotifications;
use App\Enums\Department;
use App\Models\Patient;

class PatientService
{
    public function createInsuranceProfile(Patient $patient, $data)
    {
        $profile = $patient->insurance()->create($data);

        notifyDepartment(Department::NHI->value, [
            'title' => 'New Patient Registration',
            'message' => "New Registration: {$patient->name} #{$patient->card_number}",
        ], [
            'mode' => AppNotifications::$BOTH,
        ]);
    }

    public function registerPatient($data) {}
}
