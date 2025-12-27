<?php

namespace App\Http\Controllers;

use App\Enums\AppNotifications;
use App\Enums\Department;
use App\Enums\Status;
use App\Models\Admission;
use App\Models\AdmissionTreatments;
use App\Models\ConsultationNote;
use App\Models\OperationNote;
use App\Models\Visit;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Admission::class);
        $admissions = Admission::accessibleBy($request->user())->active()->latest()->get();
        if (request()->user()->hasRole('doctor')) {
            return view('doctors.admissions.index', ['admissions' => $admissions]);
        }
        return view('nursing.admissions.index', ['admissions' => $admissions]);
    }

    public function show(Request $request, Admission $admission)
    {
        $this->authorize('view', $admission);
        if ($request->isMethod('POST')) {
            $action = $request->input('submit');
            // Log vitals
            if ($action === 'vitals') {
                $request->validate([
                    'blood_pressure' => [
                        function ($field, $value, $fail) {
                            if (!is_null($value) && !preg_match('/^\d{2,3}\/\d{2,3}$/', $value)) {
                                return $fail("Invalid format for blood pressure.");
                            }
                        }
                    ],
                    'temperature' => 'numeric',
                    'pulse' => 'numeric',
                    'respiratory_rate' => 'numeric',
                ]);

                $data = $request->except(['_token', 'vitals']);
                $data['respiration'] = $data['respiratory_rate'];
                unset($data['respiratory_rate']);

                try {
                    $admission->vitals()->create($data + ['recording_user_id' => $request->user()->id]);
                    return redirect()->back();
                } catch (\Throwable $th) {
                    report($th);
                    return back()->with('error', "An error occurred");
                }
            }

            // Log drug administrations
            if ($action === 'treatment-log') {
                $request->validate([
                    'ministered' => 'required|array|min:1'
                ]);

                // dd($request->all());
                $items = (array_keys($request->ministered));

                return redirect()->to(route('nurses.admissions.treatment-preview', $admission) . "?treatments=" . join(',', $items)); //->with('ministered', array_keys($request->ministered));
            }
        }

        $admission->load(['patient', 'ward', 'admittable', 'plan.user', 'tests', 'plan.treatments', 'delivery_note']);

        if (request()->user()->hasRole('doctor')) {
            return view('doctors.admissions.show', ['data' => $admission]);
        }

        if (request()->user()->hasRole('nurse')) {
            return view('nursing.admissions.show', ['admission' => $admission]);
        }
    }

    public function edit(Request $request, Admission $admission)
    {
        $this->authorize('update', $admission);
        $admission->plan->load(['tests', 'treatments']);

        if (!$request->isMethod('POST')) {
            return view('doctors.admissions.edit', compact('admission'));
        }
    }

    public function showPlan(Request $request, Admission $admission)
    {
        return view('doctors.admissions.view-plan', ['data' => $admission]);
    }

    public function getAdmissions(Request $request)
    {
        $this->authorize('viewAny', Admission::class);
        $admissions = Admission::accessibleBy($request->user())->with(['patient', 'ward'])->whereIn('status', [Status::active->value, Status::pending->value]);

        return $this->dataTable($request, $admissions, [
            function (&$query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search");
                });
            },
        ]);
    }

    public function wards(Request $request)
    {
        if ($request->method() !== 'POST') {
            $data = Ward::all();
            return view('it.wards', compact('data'));
        }

        $data = $request->validate([
            'name' => 'required|string|unique:wards,name',
            'beds' => 'required|integer',
            'type' => 'required|in:private,public'
        ]);

        Ward::create($data);

        return redirect()->route('it.wards');
    }

    public function assignWard(Request $request, Admission $admission)
    {
        $this->authorize('update', $admission);
        if (!$request->isMethod('POST')) {
            // $wards = Ward::whereRaw('filled_beds < beds')->get();
            $wards = Ward::all();
            $admission->load(['admittable', 'patient']);

            return view('nursing.assign-ward', ['patient' => $admission->patient, 'admission' => $admission, 'wards' => $wards]);
        }

        $request->validate([
            'ward' => 'required|integer',
        ]);

        $ward = Ward::findOrFail($request->ward);

        if ($ward->available_beds <= 0) {
            return redirect()->route('nurses.admissions.assign-ward', $admission)->with('error', "Ward {$ward->name} is already filled up.");
        }

        $ward->filled_beds = 1;
        $ward->save();

        $admission->ward_id = $ward->id;
        $admission->status = Status::active->value;
        $admission->save();

        return redirect()->route('nurses.admissions.get');
    }

    public function previewTreatment(Request $request, Admission $admission)
    {
        $this->authorize('update', $admission);
        if (!$request->isMethod('POST')) {
            $ministered = explode(',', $request->query('treatments'));
            $treatments = $admission->plan->prescription?->lines()->whereIn('id', $ministered)->get();

            if ($treatments->count() < 1) {
                return redirect()->back()->withErrors("Malformed request.");
            }

            return view('nursing.admissions.log-treatment', ['treatments' => $treatments, 'admission' => $admission]);
        }

        if ($request->has('confirm')) {
            $user = $request->user();
            $records = array_map(function ($t) use (&$user, &$admission) {
                return ['treatment_id' => $t, 'minister_id' => $user->id, 'admission_id' => $admission->id];
            }, $request->treatments);
            try {
                foreach ($records as $r) {
                    AdmissionTreatments::create($r);
                }
                return redirect()->to(route('nurses.admissions.show', $admission));
            } catch (\Throwable $th) {
                report($th);
                return redirect()->back()->withErrors($th->getMessage());
            }
        }
    }

    public function createAdmission(Request $request, Visit $visit)
    {
        $this->authorize('create', Admission::class);
        $request->validate([
            'indication' => 'required|string',
            'note' => 'nullable|string',
        ]);

        if (!empty($visit->admission) && ($visit->admission->status != Status::cancelled->value || $visit->admission->status != Status::closed->value)) {
            return response()->json([
                'message' => "There is still an ongoing admission for this patient",
                'ok' => false,
            ]);
        }

        $admission = $visit->admission()->create([
            'visit_id' => $visit->id,
            'patient_id' => $visit->patient_id,
            'admittable_type' => $visit::class,
            'admittable_id' => $visit->id,
            'status' => Status::pending->value,
        ]);

        $plan = $admission->plans()->create([
            'user_id' => $request->user()->id,
            ...$request->except('_token'),
            'status' => Status::active->value,
        ]);

        // ! Redirect all tests to show up for the admission
        $visit->tests->each(fn($test) => $test->update([
            'testable_type' => $admission::class,
            'testable_id' => $admission->id,
        ]));

        // ! Redirect all prescriptions to show up for the admission plan
        $visit->treatments->each(fn($t) => $t->update([
            'event_type' => $plan::class,
            'event_id' => $plan->id,
        ]));

        // TODO: Redirect all scan requests to Radiology
        // * $visit->radios->each(fn ($scan) => $scan->update([]));

        $visit->update(['status' => Status::Unavailable->value]);

        notifyDepartment(Department::NUR->value, "{$visit->patient->name} #[{$visit->patient->card_number}] is being admitted.", [
            'mode' => AppNotifications::$BOTH,
        ]);

        return response()->json([
            'admission' => $admission,
            'plan' => $plan,
            'patient' => $visit->patient,
            'ok' => true,
        ]);
    }

    public function discharge(Request $request, Admission $admission)
    {
        $this->authorize('update', $admission);
        $request->validate([
            'discharge_summary' => 'required|string',
            'discharged_on' => 'required',
        ]);

        $admission->discharged_on ??= $request->input('discharged_on');
        $admission->status = Status::closed->value;
        $admission->discharge_summary ??= $request->input('discharge_summary');
        $admission->save();

        $ward = $admission->ward;
        if ($ward) {
            $ward->filled_beds = max(0, $ward->filled_beds - 1);
            $ward->save();
        }

        return redirect()->route('nurses.admissions.get');
    }

    public function reviewNote(Request $request, Admission $admission)
    {
        return view('doctors.admissions.review', compact('admission'));
    }

    public function saveOperationNote(Request $request, Admission $admission)
    {
        $admission->load(['patient']);
        $data = $request->validate([
            'unit' => 'required|string',
            'consultant' => 'required|string',
            'operation_date' => 'required|string',
            'surgeons' => 'required|string',
            'assistants' => 'required|string',
            'scrub_nurse' => 'required|string',
            'circulating_nurse' => 'nullable|string',
            'anaesthesists' => 'nullable|string',
            'anaesthesia_type' => 'nullable|string',
            'indication' => 'required|string',
            'incision' => 'nullable|string',
            'findings' => 'required|string',
            'procedure' => 'required|string',
        ]);

        try {
            //code...
            $note = OperationNote::create([
                'admission_id' => $admission->id,
                'patient_id' => $admission->patient->id,
                'user_id' => auth()->user()->id,
                ...$data,
            ]);
            return response()->json(['op_note' => $note, 'message' => 'Success']);
        } catch (\Throwable $th) {
            report($th);
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function saveDeliveryNote(Request $request, Admission $admission)
    {
    }

    public function getOpNote(Request $request, OperationNote $opnote)
    {
        $opnote->load(['user', 'patient']);
        return view('doctors.admissions.opnote', compact('opnote'));
    }

    public function updateNote(Request $request, ConsultationNote $note)
    {
        $note->update($request->validate([
            'note' => 'required|string',
        ]));

        return response()->json([
            'message' => 'Success',
        ]);
    }

    public function setForDischarge(Request $request, Admission $admission)
    {
        $request->validate([
            'discharged_on' => 'required|string',
            'discharge_summary' => 'nullable|string',
        ]);

        if ($request->input('discharge_summary')) {
            $admission->status = Status::closed->value;
            $admission->discharge_summary = $request->input('discharge_summary');
        } else {
            $admission->status = Status::ejected->value;
        }

        $admission->discharged_on = $request->input('discharged_on');
        $admission->ward->filled_beds--;
        $admission->ward->save();
        $admission->save();

        return response()->json([
            'message' => 'Success',
            'data' => $admission,
        ]);
    }
}
