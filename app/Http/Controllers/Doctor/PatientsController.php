<?php

namespace App\Http\Controllers\Doctor;

use App\Enums\Department as EnumsDepartment;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AncVisit;
use App\Models\Department;
use App\Models\Documentation;
use App\Models\Visit;
use App\Notifications\StaffNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientsController extends Controller
{
    public function treat(Request $request, Visit $visit)
    {
        if ($request->method() !== 'POST') return view('doctors.consultation-form', compact('visit'));

        $data = $request->validate([
            'symptoms' => 'nullable|string',
            'prognosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'comment' => 'nullable|string',
            'tests' => 'array',
            'tests.*' => 'string',
            'treatments' => 'array',
            'treatments.*' => 'string',
            'dosage' => 'array',
            'dosage.*' => 'string',
            'duration' => 'array',
            'duration.*' => 'string',
            'next_visit' => 'nullable|date',
        ]);

        $data['tests'] = array_unique($request->tests);

        DB::beginTransaction();

        try {
            $doc = Documentation::create([
                ...$data,
                'visit_id' => $visit->id,
                'user_id' => $request->user()->id,
                'patient_id' => $visit->patient_id,
            ]);

            foreach ($data['tests'] as $test) {
                $doc->tests()->create(['name' => $test, 'status' => Status::pending->value]);
            }

            foreach ($data['treatments'] as $tIndex => $t) {
                $doc->treatments()->create([
                    'name' => $t,
                    'dosage' => $data['dosage'][$tIndex],
                    'duration' => $data['duration'][$tIndex],
                    'status' => Status::pending->value,
                    'requested_by' => $request->user()->id,
                ]);
            }

            DB::commit();

            $visit->awaiting_doctor = false;

            if (count($data['tests']) > 0) {
                $visit->awaiting_lab_results = true;
            }

            if (count($data['treatments']) > 0) {
                $visit->awaiting_pharmacy = true;
            }

            $visit->save();

            return redirect()->route('dashboard');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $th->getMessage()]);
        }
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
