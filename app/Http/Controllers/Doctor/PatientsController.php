<?php

namespace App\Http\Controllers\Doctor;

use App\Enums\Department as EnumsDepartment;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AncVisit;
use App\Models\AntenatalProfile;
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

            if (count($data['tests']) > 0) {
                foreach ($data['tests'] as $test) {
                    $doc->tests()->create(['name' => $test, 'status' => Status::pending->value, 'patient_id' => $visit->patient_id]);
                }
                Department::find(EnumsDepartment::LAB->value)?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));
                $visit->awaiting_lab_results = true;
            }

            if (count($data['treatments']) > 0) {
                foreach ($data['treatments'] as $tIndex => $t) {
                    $doc->treatments()->create([
                        'name' => $t,
                        'dosage' => $data['dosage'][$tIndex],
                        'duration' => $data['duration'][$tIndex],
                        'status' => Status::pending->value,
                        'requested_by' => $request->user()->id,
                        'patient_id' => $visit->patient_id,
                    ]);
                }
                Department::find(EnumsDepartment::PHA->value)?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));
                $visit->awaiting_pharmacy = true;
            }

            $visit->awaiting_doctor = false;
            $visit->save();
            DB::commit();

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

    public function followUp(Request $request, Documentation $documentation)
    {
        if ($request->method() !== 'POST') return view('doctors.follow-up', compact('documentation'));
    }

    public function pendingAncBookings(Request $request)
    {
        return view('doctors.anc-bookings');
    }

    public function submitAncBooking(Request $request, AntenatalProfile $profile)
    {
        if ($request->method() == 'GET') {
            return view('doctors.anc-booking-form', compact('profile'));
        }

        $request->validate([
            'gravida' => 'nullable|numeric',
            'parity' => 'nullable|numeric',
            'fundal_height' => 'nullable|numeric',
            'fetal_heart_rate' => 'nullable|numeric',
            'presentation' => 'nullable|string',
            'lie' => 'nullable|string',
            'presentation' => 'nullable|string',
            'presentation_relationship' => 'nullable|string',
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

        $user = $request->user();
        DB::beginTransaction();

        try {
            $profile->update($request->except(
                'tests',
                'treatments',
                'dosage',
                'duration',
                'next_visit'
            ) + [
                'doctor_id' => $user->id
            ]);

            if (count($request->tests ?? []) > 0 || count($request->treatments ?? []) > 0) {
                $visit = new Visit([
                    'patient_id' => $profile->patient_id,
                    'visit_type' => AncVisit::class,
                    'visit_id' => 0,
                    'awaiting_vitals' => false,
                    'awaiting_doctor' => true,
                ]);

                $visit->vitals = ['data' => $profile->getVitals(), 'staff' => null];

                $ancVisit = AncVisit::create([
                    'patient_id' => $profile->patient_id,
                    'doctor_id' => $user->id,
                    'fundal_height' => $request->fundal_height,
                    'fetal_heart_rate' => $request->fetal_heart_rate,
                    'lie' => $request->lie,
                    'presentation_relationship' => $request->presentation,
                    'presentation' => $request->presentation,
                ]);

                $visit->visit_id = $ancVisit->id;
                $visit->save();

                $documentation = $visit->documentations()->create([
                    'visit_id' => $visit->id,
                    'patient_id' => $profile->patient_id,
                    'user_id' => $user->id,
                ]);

                $visit->awaiting_tests = $this->processAndSaveAncTests($request->tests ?? [], $documentation);
                $visit->awaiting_pharmacy = $this->processAndSaveAncTreatments($request, $documentation, $user->id);
                $visit->save();
            }

            if ($request->has('completed')) {
                $profile->awaiting_doctor = false;
            }

            $profile->save();

            DB::commit();
            return redirect()->route('dashboard');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $th->getMessage()]);
        }
    }

    private function processAndSaveAncTests($tests, Documentation &$doc)
    {
        foreach ($tests as $test) {
            $doc->tests()->create([
                'name' => $test,
                'patient_id' => $doc->patient_id,
            ]);
        }
        if (count($tests) > 0) return true;
    }

    private function processAndSaveAncTreatments(Request $request, Documentation &$doc, $id = null)
    {
        foreach ($request->treatments ?? [] as $i => $t) {
            $doc->treatments()->create([
                'name' => $t,
                'dosage' => $request->dosage[$i],
                'duration' => $request->duration[$i],
                'patient_id' => $doc->patient_id,
                'dispensed_by' => $id
            ]);
        }
        if (count($request->treatments ?? []) > 0) return true;
    }
}
