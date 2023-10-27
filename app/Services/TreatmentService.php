<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Visit;
use App\Models\Department;
use App\Models\Documentation;
use App\Models\PatientImaging;
use Illuminate\Support\Facades\DB;
use App\Models\PatientExaminations;
use App\Notifications\StaffNotification;
use App\Enums\Department as EnumsDepartment;
use App\Models\AncVisit;
use App\Models\DocumentationPrescription;
use App\Models\User;
use Carbon\CarbonInterface;

class TreatmentService
{
    public function __construct()
    {
    }

    public function saveTreatment(Visit $visit, $data, User $treater, Documentation $doc = null)
    {
        $data['tests'] = array_unique($data['tests'] ?? []);
        $complaints = $data['complaints'] ?? [];
        $history = $data['history'] ?? [];

        DB::beginTransaction();

        try {
            $doc ??= Documentation::create([
                ...$data,
                'symptoms' => count($complaints) > 0 ? implode(',', $complaints) : null,
                'visit_id' => $visit->id,
                'user_id' => $treater?->id,
                'patient_id' => $visit->patient_id,
            ]);

            // Save complaints
            foreach ($complaints as $c) {
                $doc->complaints()->create(['name' => $c]);
            }

            // Save history of complaints
            if ($history) {
                $doc->complaints_history = $history;
            }

            if (count($data['tests']) > 0) {
                foreach ($data['tests'] as $test) {
                    $doc->tests()->create(['name' => $test, 'status' => Status::pending->value, 'patient_id' => $visit->patient_id]);
                }
                Department::find(EnumsDepartment::LAB->value)?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));
                $visit->awaiting_lab_results = true;
            }

            if (count($data['imgs'] ?? []) > 0) {
                foreach ($data['imgs'] as $img) {
                    PatientImaging::create([
                        'name' => $img,
                        'type' => null,
                        'status' => Status::pending->value,
                        'patient_id' => $visit->patient_id,
                        'documentation_id' => $doc->id,
                        'requested_by' => $treater->id,
                    ]);
                }
                Department::find(EnumsDepartment::RAD->value)?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room and has some scans to be performed. Please attend to them"));
                $visit->awaiting_radiology = true;
            }

            if (count($data['treatments'] ?? []) > 0) {
                foreach ($data['treatments'] as $tIndex => $t) {
                    $doc->treatments()->create([
                        'name' => $t,
                        'dosage' => $data['dosage'][$tIndex],
                        'frequency' => $data['frequency'][$tIndex],
                        'duration' => $data['duration'][$tIndex],
                        'status' => Status::pending->value,
                        'requested_by' => $treater->id,
                        'patient_id' => $visit->patient_id,
                    ]);
                }
                Department::find(EnumsDepartment::PHA->value)?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));
                Department::find(EnumsDepartment::DIS->value)?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has been prescribed some medication. Please submit a quote."));
                $visit->awaiting_pharmacy = true;
            }

            $exams = new PatientExaminations([]);

            $exams->patient_id = $visit->patient_id;
            $exams->documentation_id = $doc->id;
            $exams->general = $data['physical_exams'];
            $exams->specifics = collect($data)->only(['head_and_neck', 'chest', 'abdomen', 'muscle_skeletal', 'vaginal_digital_rectal']);
            $exams->save();

            $doc->save();

            $visit->awaiting_doctor = false;
            $visit->save();
            DB::commit();

            return $doc;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function admitPatient()
    {
    }

    public function treatAnc(AncVisit $ancVisit, $data, $doctor_id)
    {
        $visit = $ancVisit->visit;
        $ancVisit->update($data + ['doctor_id' => $doctor_id]);

        $visit->awaiting_doctor = false;
        $visit->awaiting_pharmacy = true;
        $visit->save();

        $drugs = $data['drugs'] ?? "";
        $drugs = explode(',', $drugs);

        if (count($drugs) > 0) {
            foreach ($drugs as $drug) {
                try {
                    DocumentationPrescription::create([
                        'name' => $drug,
                        'dosage' => '',
                        'frequency' => '',
                        'duration' => '',
                        'status' => Status::pending->value,
                        'requested_by' => $doctor_id,
                        'patient_id' => $visit->patient_id,
                        'prescriptionable_type' => $ancVisit::class,
                        'prescriptionable_id' => $ancVisit->id,
                    ]);
                } catch (\Throwable $th) {
                    report($th);
                }
            }
            $pharmacy = Department::find(EnumsDepartment::PHA->value);
            $pharmacy?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));
        }

        $records = Department::find(EnumsDepartment::REC->value);
        $records->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));
    }
}
