<?php

namespace App\Policies;

use App\Models\ConsultationNote;
use App\Models\User;

class ConsultationNotePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function edit(User $user, ConsultationNote $note)
    {
        if (in_array($user->phone, config('app.generic_doctor_profiles'))) {
            return false;
        }

        if (isset($note->recorder)) {
            return session(config('app.generic_doctor_id')) == $note->recorder->recorder;
        }

        return $user->id == $note->consultant_id;
    }

    public function delete(User $user, ConsultationNote $note)
    {
        return $this->edit($user, $note);
    }
}
