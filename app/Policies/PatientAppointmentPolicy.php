<?php

namespace App\Policies;

use App\Models\PatientAppointment;
use App\Models\User;

class PatientAppointmentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function inspect(User $user, PatientAppointment $appointment)
    {
        return $user->hasRole(['nurse', 'doctor', 'insurance', 'admin']);
    }

    public function begin_appointment(User $user, PatientAppointment $appointment)
    {
        return $user->hasRole(['doctor', 'record']);
    }
}
