<?php

namespace App\Services;

use App\Enums\AppNotifications;
use App\Enums\Status;
use App\Models\Visit;
use App\Models\Documentation;
use Illuminate\Support\Facades\DB;
use App\Models\PatientExaminations;
use App\Enums\Department;
use App\Interfaces\Documentable;
use App\Models\Admission;
use App\Models\AncVisit;
use App\Models\Patient;
use App\Models\User;

class TreatmentService
{
    public function __construct() {}

    private function saveDiagnoses(Documentable|Documentation|AncVisit &$doc, $data, $doctor_id)
    {
        // if (isset($data['prognosis'])) {
        //     $doc->diagnoses()->create([
        //         'diagnoses' => $data['prognosis'],
        //         'patient_id' => $doc->patient_id,
        //         'user_id' => $doctor_id
        //     ]);
        // }
        if (isset($data['diagnosis'])) {
            foreach ($data['diagnosis'] as $d) {
                $doc->diagnoses()->create([
                    'diagnoses' => $d,
                    'patient_id' => $doc->patient_id,
                    'user_id' => $doctor_id
                ]);
            }
        }
    }

    private function saveComplaints(Documentable|Documentation|AncVisit &$doc, $complaints = [], $history = null)
    {
        // Save complaints
        foreach ($complaints as $c) {
            $doc->complaints()->create(['name' => $c['name'], 'duration' => $c['duration']]);
        }

        // Save history of complaints
        if ($history) {
            $doc->complaints_history = $history;
        }
    }

    private function saveTests(Documentable|Documentation|AncVisit &$doc, $tests)
    {
        foreach ($tests as $test) {
            $doc->tests()->create(['name' => $test, 'status' => Status::pending->value, 'patient_id' => $doc->patient_id]);
        }
    }

    private function saveImagings(Documentable|Documentation|AncVisit &$doc, $imgs, $treater_id = null)
    {
        foreach ($imgs as $img) {
            $doc->radios()->create([
                'name' => $img,
                'type' => null,
                'status' => Status::pending->value,
                'patient_id' => $doc->patient_id,
                'documentation_id' => $doc->id,
                'requested_by' => $treater_id,
            ]);
        }
    }

    private function savePrescriptions(Documentable|Documentation|AncVisit &$doc, $data, $treater_id = null)
    {
        foreach ($data['treatments'] as $tIndex => $t) {
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

    public function saveTreatment(Visit $visit, $data, User $treater, ?Documentation $doc = null)
    {
        $data['tests'] = array_unique($data['tests'] ?? []);
        $complaints = $data['complaints'] ?? [];
        $history = $data['history'] ?? [];

        DB::beginTransaction();

        try {
            $doc ??= Documentation::create([
                ...$data,
                'symptoms' => "", //count($complaints) > 0 ? implode(',', $complaints) : null,
                'visit_id' => $visit->id,
                'user_id' => $treater?->id,
                'patient_id' => $visit->patient_id,
            ]);

            $this->saveComplaints($doc, $complaints, $history);

            if (count($data['tests']) > 0) {
                $this->saveTests($doc, $data['tests']);
                notifyDepartment(Department::LAB->value, [
                    'title' => 'New Lab Tests Requested',
                    'message' => "<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them",
                ], [
                    'mode' => AppNotifications::$IN_APP,
                ]);
                $visit->awaiting_lab_results = true;
            }

            if (count($data['imgs'] ?? []) > 0) {
                $this->saveImagings($doc, $data['imgs'], $treater->id);
                notifyDepartment(Department::RAD->value, [
                    'title' => 'New Radiology Requests',
                    'message' => "<u>{$visit->patient->name}</u> has left the consulting room and has some scans to be performed. Please attend to them",
                ], [
                    'mode' => AppNotifications::$BOTH,
                ]);
                $visit->awaiting_radiology = true;
            }

            if (count($data['treatments'] ?? []) > 0) {
                $this->savePrescriptions($doc, $data, $treater->id);
                notifyDepartment(Department::DIS->value, [
                    'title' => 'New Prescription',
                    'message' => "<u>{$visit->patient->name}</u> has left the consulting room and requires your attention.",
                ], [
                    'mode' => AppNotifications::$BOTH,
                ]);
                $visit->awaiting_pharmacy = true;
            }

            $this->saveDiagnoses($doc, $data, $treater->id);

            $exams = new PatientExaminations([]);

            $exams->patient_id = $visit->patient_id;
            $exams->documentation_id = $doc->id;
            $exams->general = $data['physical_exams'];
            $exams->specifics = collect($data)->only(['head_and_neck', 'chest', 'abdomen', 'muscle_skeletal', 'vaginal_digital_rectal']);
            $exams->save();

            $doc->save();

            if ($data['admit'] === true) {
                $this->admitPatient($doc);
            }

            $visit->awaiting_doctor = false;
            $visit->save();
            DB::commit();

            return $doc;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function admitPatient(Documentable|Documentation|AncVisit &$record)
    {
        $admission = Admission::create([
            'patient_id' => $record->patient_id,
            'visit_id' => $record->visit_id,
            'admittable_type' => $record::class,
            'admittable_id' => $record->id,
        ]);

        return $admission;
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
            $this->savePrescriptions($ancVisit, $data, $doctor_id);

            notifyDepartment(Department::DIS->value, [
                'title' => 'New Prescription',
                'message' => "<u>{$visit->patient->name}</u> has left the consulting room and requires your attention.",
            ], [
                'mode' => AppNotifications::$BOTH,
            ]);
        }

        if (count($data['imgs'] ?? []) > 0) {
            $this->saveImagings($ancVisit, $data['imgs'], $doctor_id);
        }

        if (count($data['tests'] ?? []) > 0) {
            $this->saveTests($ancVisit, $data['tests']);
        }

        notifyDepartment(Department::REC->value, [
            'title' => "Update: {$visit->patient->name}",
            'message' => "<u>{$visit->patient->name}</u> has left the consulting room. Please attend to them",
        ], [
            'mode' => AppNotifications::$BOTH,
        ]);

        return $ancVisit;
    }
}
