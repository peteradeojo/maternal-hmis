<?php

namespace App\Http\Controllers\Doctor;

use App\Enums\Department as EnumsDepartment;
use App\Http\Controllers\Controller;
use App\Models\AncVisit;
use App\Models\Department;
use App\Models\Visit;
use App\Notifications\StaffNotification;
use Illuminate\Http\Request;

class PatientsController extends Controller
{
    public function treat(Request $request, Visit $visit)
    {
        if ($request->method() !== 'POST') return view('doctors.consultation-form', compact('visit'));
    }

    public function treatAnc(Request $request, AncVisit $visit)
    {
        $data = $request->validate([
            'fundal_height' => 'nullable|string',
            'fetal_heart_rate' => 'nullable|string',
            'presentation' => 'nullable|string',
            'lie' => 'nullable|string',
            'presentation_relationship' => 'nullable|string',
            'return_visit' => 'nullable|date',
            'complaints' => 'nullable|string',
            'drugs' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $visit->update($data + ['doctor_id' => $request->user()->id]);

        $visit->visit->awaiting_doctor = false;
        $visit->visit->awaiting_pharmacy = true;
        $visit->visit->save();

        $pharmacy = Department::find(EnumsDepartment::PHA->value);
        $pharmacy?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));

        $records = Department::find(EnumsDepartment::REC->value);
        $records->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));

        return redirect()->route('dashboard');
    }
}
