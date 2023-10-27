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
use App\Interfaces\Documentable;
use App\Models\AncVisit;
use App\Models\User;

class TreatmentService
{
    public function __construct()
    {
    }

    private function saveComplaints(Documentable &$doc, $complaints = [], $history = "")
    {
        // Save complaints
        foreach ($complaints as $c) {
            $doc->complaints()->create(['name' => $c]);
        }

        // Save history of complaints
        if ($history) {
            $doc->complaints_history = $history;
        }
    }

    private function saveTests(Documentable &$doc, $tests)
    {
        foreach ($tests as $test) {
            $doc->tests()->create(['name' => $test, 'status' => Status::pending->value, 'patient_id' => $doc->patient_id]);
        }
    }

    private function saveImagings(Documentable &$doc, $imgs, $treater_id = null)
    {
        foreach ($imgs as $img) {
            PatientImaging::create([
                'name' => $img,
                'type' => null,
                'status' => Status::pending->value,
                'patient_id' => $doc->patient_id,
                'documentation_id' => $doc->id,
                'requested_by' => $treater_id,
            ]);
        }
    }

    private function savePrescriptions(Documentable &$doc, $data, $treater_id = null)
    {
        foreach ($data as $tIndex => $t) {
            $doc->treatments()->create([
                'name' => $t,
                'dosage' => $data['dosage'][$tIndex],
                'frequency' => $data['frequency'][$tIndex],
                'duration' => $data['duration'][$tIndex],
                'route' => $data['route'][$tIndex],
                'status' => Status::pending->value,
                'requested_by' => $treater_id,
                'patient_id' => $doc->patient_id,
            ]);
        }
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

            $this->saveComplaints($doc, $complaints, $history);

            if (count($data['tests']) > 0) {
                $this->saveTests($doc, $data['tests']);
                Department::find(EnumsDepartment::LAB->value)?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));
                $visit->awaiting_lab_results = true;
            }

            if (count($data['imgs'] ?? []) > 0) {
                $this->saveImagings($doc, $data['imgs'], $treater->id);
                Department::find(EnumsDepartment::RAD->value)?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room and has some scans to be performed. Please attend to them"));
                $visit->awaiting_radiology = true;
            }

            if (count($data['treatments'] ?? []) > 0) {
                $this->savePrescriptions($doc, $data['treatments'], $treater->id);
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

        if (count($data['treatments'] ?? []) > 0) {
            $this->savePrescriptions($doc, $data['treatments'], $doctor_id);

            $pharmacy = Department::find(EnumsDepartment::PHA->value);
            $pharmacy?->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));
        }

        if (count($data['imgs']) > 0) {
            $this->saveImagings($doc, $data['imgs'], $doctor_id);
        }

        if (count($data['tests']) > 0) {
            $this->saveTests($doc, $data['tests'], $doctor_id);
        }

        $records = Department::find(EnumsDepartment::REC->value);
        $records->notifyParticipants(new StaffNotification("<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them"));
    }
}
