<?php

namespace App\Http\Controllers;

use App\Enums\AppNotifications;
use App\Enums\Department as EnumsDepartment;
use App\Models\Visit;
use Illuminate\Http\Request;

class VitalsController extends Controller
{
    public function takeVitals(Request $request, Visit $visit)
    {
        if ($request->method() != 'POST') {
            return view('nursing.patient-vitals', compact('visit'));
        }

        $request->validate([
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
            'spo2' => 'nullable|numeric',
        ]);

        $visit->vitals()->create($request->except('_token', 'respiratory_rate') + [
            'respiration' => $request->respiratory_rate,
            'recording_user_id' => auth()->user()->id,
        ]);

        notifyDepartment(EnumsDepartment::DOC->value, [
            'title' => 'New Vitals Recorded',
            'message' => "Vitals taken for {$visit->patient->name}. Patient is ready to see the doctor.",
        ], [
            'mode' => AppNotifications::$BOTH,
        ]);

        return redirect()->to('/')->with('success', 'Vitals taken successfully');
    }

    public function index(Request $request)
    {
        return view('nursing.vitals');
    }
}
