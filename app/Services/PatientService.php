<?php

namespace App\Services;

use App\Enums\AppNotifications;
use App\Enums\Department;
use App\Models\InsuranceOrganization;
use App\Models\Patient;

class PatientService
{
    public function updateInsuranceProfile(Patient $patient, $data) {}

    public function createInsuranceProfile(Patient $patient, $data)
    {
        $hmo_name = InsuranceOrganization::find($data['orgid']);
        $profile = $patient->insurance()->create([...$data, 'hmo_name' => $hmo_name->name]);

        notifyDepartment(Department::NHI->value, [
            'title' => 'New Patient Registration',
            'message' => "New Registration: {$patient->name} #{$patient->card_number}",
        ], [
            'mode' => AppNotifications::$BOTH,
        ]);

        return $profile;
    }

    public function registerPatient($data) {}
}
