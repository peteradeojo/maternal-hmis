<?php

namespace App\Http\Controllers;

use App\Enums\Department as EnumsDepartment;
use App\Models\Department;
use App\Models\Visit;
use App\Models\Vitals;
use App\Notifications\StaffNotification;
use Illuminate\Http\Request;

class VitalsController extends Controller
{
    public function takeVitals(Request $request, Visit $visit)
    {
        if ($request->method() != 'POST') {
            return view('nursing.patient-vitals', compact('visit'));
        }

        $data = $request->validate([
            'temperature' => 'nullable|numeric',
            'blood_pressure' => ['nullable', function ($attr, $value, $fail) {
                if (!preg_match('/^\d{2,3}\/\d{2,3}$/', $value)) {
                    $fail('Invalid blood pressure format');
                }
            }],
            'pulse' => 'nullable|numeric',
            'respiratory_rate' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
        ]);

        $visit->vitals()->create($request->except('_token', 'respiratory_rate') + [
            'respiration' => $request->respiratory_rate,
            'recording_user_id' => auth()->user()->id,
        ]);

        /**
         * @var Department
         */
        $consultants = Department::where('id', EnumsDepartment::DOC->value)->first();
        $consultants?->notifyParticipants(new StaffNotification("Vitals taken for {$visit->patient->name}. Patient is ready to see the doctor."));

        return redirect()->to('/')->with('success', 'Vitals taken successfully');
    }

    public function index(Request $request)
    {
        return view('nursing.vitals');
    }
}
