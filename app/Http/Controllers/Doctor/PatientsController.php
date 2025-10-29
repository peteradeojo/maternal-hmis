<?php

namespace App\Http\Controllers\Doctor;

use App\Enums\Department as EnumsDepartment;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AncVisit;
use App\Models\AntenatalProfile;
use App\Models\Department;
use App\Models\Documentation;
use App\Models\DocumentationComplaints;
use App\Models\DocumentationPrescription;
use App\Models\DocumentationTest;
use App\Models\DocumentedDiagnosis;
use App\Models\Patient;
use App\Models\PatientExaminations;
use App\Models\PatientImaging;
use App\Models\Product;
use App\Models\Visit;
use App\Notifications\StaffNotification;
use App\Services\TreatmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientsController extends Controller
{
    public function __construct(private TreatmentService $treatmentService)
    {
    }

    public function index(Request $request)
    {
        return view('doctors.patients');
    }

    private function loadAutoCompleteData()
    {
        // $complaints = DocumentationComplaints::selectRaw('DISTINCT name')->get()->toArray();
        // $prescriptions = Product::whereHas('category', function ($q) {
        //     $q->where('department_id', 4);
        // })->get()->pluck('name', 'id')->toArray();

        // $tests = Product::whereHas('category', function ($q) {
        //     $q->where('department_id', 5);
        // })->get()->pluck('name', 'id')->toArray();

        $diagnoses = DocumentedDiagnosis::selectRaw('DISTINCT diagnoses as name')->get()->toArray();
        $data = compact('diagnoses');
        return $data;
    }

    public function treat(Request $request, Visit $visit)
    {
        if ($request->method() !== 'POST') {
            $data = $this->loadAutoCompleteData();
            $visit->patient->load(['visits', 'notes.consultant', 'antenatalProfiles']);
            return view('doctors.visit-form-2', [...$data, 'visit' => $visit]);
            // return view('doctors.consultation-form', [...$data, 'visit' => $visit]);
        }

        $data = $request->except('_token');
        if (count(array_filter($data)) < 1) {
            return back()->withInput()->withErrors(['error' => 'Please fill at least one field']);
        }

        $request->mergeIfMissing(['tests' => [], 'admit' => false]);
        $data = $request->except('_token');

        DB::beginTransaction();

        try {
            // $request->filled('admit') && $data['admit'] = true;
            $data['admit'] = $data['admit'] !== false;

            $documentation = $this->treatmentService->saveTreatment($visit, $data, $request->user());

            DB::commit();
            return redirect()->route('dashboard');
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);
            return redirect()->back()->with('error', $th->getMessage());
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
            // 'complaints' => 'nullable|string',
            // 'drugs' => 'nullable|string',
        ]);

        try {
            $visit->update($request->all());
            $visit->visit->awaiting_doctor = false;
            $visit->visit->status = Status::completed->value;

            $visit->visit->save();
            return redirect()->route('dashboard');
        } catch (\Throwable $th) {
            report($th);
            return redirect()->route('dashboard')->with('error', $th->getMessage());
        }
    }

    public function followUp(Request $request, Documentation $documentation)
    {
        if ($request->method() !== 'POST') return view('doctors.follow-up', compact('documentation'));


        $data = $request->except('_token');
        if (count(array_filter($data)) < 1) {
            return back()->withInput()->withErrors(['error' => 'Please fill at least one field']);
        }

        $request->mergeIfMissing(['tests' => [], 'admit' => false]);

        $visit = $documentation->visit;

        try {
            // $request->filled('admit') ? ($data['admit'] = true) : ($data['admit'] = false);
            $this->treatmentService->saveTreatment($visit, $request->all(), $request->user());

            return redirect()->route('dashboard');
        } catch (\Throwable $th) {
            report($th);
            return redirect()->back()->with('error', $th->getMessage());
        }
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

        // dd($request->all());

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

                // $documentation = $visit->documentations()->create([
                //     'visit_id' => $visit->id,
                //     'patient_id' => $profile->patient_id,
                //     'user_id' => $user->id,
                // ]);

                $visit->awaiting_tests = $this->processAndSaveAncTests($request->tests ?? [], $ancVisit);
                $visit->awaiting_pharmacy = $this->processAndSaveAncTreatments($request, $ancVisit, $user->id);
                $visit->save();
            }

            if ($request->has('completed')) {
                $profile->awaiting_doctor = false;
            }

            $profile->save();

            DB::commit();
            if ($request->has('go_back')) return redirect()->back();
            return redirect()->route('dashboard');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $th->getMessage()]);
        }
    }

    private function processAndSaveAncTests($tests, AncVisit &$doc)
    {
        foreach ($tests as $test) {
            $doc->tests()->create([
                'name' => $test,
                'patient_id' => $doc->patient_id,
            ]);
        }
        if (count($tests) > 0) return true;
        return false;
    }

    private function processAndSaveAncTreatments(Request $request, Documentation|AncVisit &$doc, $id = null)
    {
        foreach ($request->treatments ?? [] as $i => $t) {
            $doc->treatments()->create([
                'name' => $t,
                'dosage' => $request->dosage[$i],
                'frequency' => $request->frequency[$i],
                'duration' => $request->duration[$i],
                'patient_id' => $doc->patient_id,
                'dispensed_by' => $id
            ]);
        }
        if (count($request->treatments ?? []) > 0) return true;
        return false;
    }

    public function fetchPatients(Request $request)
    {
        return $this->dataTable($request, Patient::with(['category']), [
            function ($query, $search) {
                $query->where('name', 'like', "$search%");
            }
        ]);
    }

    public function show(Request $request, Patient $patient)
    {
        return view('doctors.show-patient', ['patient' => $patient]);
    }

    public function history(Request $request)
    {
        return view('doctors.visits.history');
    }

    public function getVisitsHistory(Request $request)
    {
        $visits = Visit::with(['patient.category', 'visit'])->whereIn('status', [Status::completed->value, Status::closed->value, Status::ejected->value])->latest();

        return $this->dataTable($request, $visits, [
            function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            }
        ]);
    }

    public function visit(Request $request, Visit $visit)
    {
        $visit->load(['visit', 'patient']);

        // $documentations = $visit->documentations; //Documentation::where('visit_id', $visit->id)->with(['tests', 'treatments', 'diagnoses'])->get();

        if ($request->has('brief')) {
            return view('doctors.components.history-report', ['visit' => $visit->visit]);
        }
        return view('doctors.visits.show', compact('visit'));
    }

    // public function startAdmission(Request $request, Visit $visit)
    // {
    //     $visit->load(['patient']);
    //     if (!$request->isMethod('POST')) {
    //         return view('doctors.admissions.start', compact('visit'));
    //     }

    //     try {
    //         $this->treatmentService->startAdmission($request->all(), $visit->patient);
    //         return redirect()->route('dashboard');
    //     } catch (\Throwable $th) {
    //         report($th);
    //         return redirect()->back()->with('error', $th->getMessage());
    //     }
    // }

    public function note(Request $request, Visit $visit)
    {
        $request->validate([
            'note' => 'required|string'
        ]);

        $visit->notes()->create([
            'note' => $request->note,
            'patient_id' => $visit->patient_id,
            'consultant_id' => $request->user()->id,
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }

    public function saveDiagnosis(Request $request, Visit $visit)
    {
        $request->validate(['diagnosis' => 'required|string']);

        $visit->visit->diagnoses()->create([
            'user_id' => $request->user()->id,
            'patient_id' => $visit->patient_id,
            'diagnoses' =>  $request->diagnosis,
        ]);

        return response()->json([
            'ok' => true
        ]);
    }

    public function addExamination(Request $request, Visit $visit)
    {
        $request->validate([
            'physical_exams' => 'nullable|string',
            'abdomen' => 'nullable|string',
            'chest' => 'nullable|string',
            'head_and_neck' => 'nullable|string',
            'muscle_skeletal' => 'nullable|string',
            'vaginal_digital_rectal' => 'nullable|string',
        ]);

        $physical = $request->physical_exams;
        $other = $request->except('_token', 'physical_exams');

        if ($visit->visit->examination()->exists()) {
            $visit->visit->examination()->update([
                'general' => $physical,
                'specifics' => $other,
            ]);
        } else {
            $exam = $visit->visit->examination()->create([
                'patient_id' => $visit->patient_id,
                'general' => $physical,
                'specifics' => $other,
            ]);
        }


        return json_encode(compact('physical',  'other'));
    }
}
