<?php

namespace App\Http\Controllers;

use App\Enums\Department;
use App\Enums\Status;
use App\Models\Admission;
use App\Models\AdmissionTreatments;
use App\Models\Visit;
use App\Models\Ward;
use Illuminate\Http\Request;

class AdmissionsController extends Controller
{
    public function index(Request $request)
    {
        $admissions = Admission::whereNull('discharged_on')->latest('updated_at')->limit(10)->get();
        if (request()->user()->department_id == Department::DOC->value) {
            return view('doctors.admissions.index', ['admissions' => $admissions]);
        }
        return view('nursing.admissions.index', ['admissions' => $admissions]);
    }

    public function show(Request  $request, Admission $admission)
    {
        if ($request->isMethod('POST')) {

            $action = $request->input('submit');
            // Log vitals
            if ($action === 'vitals') {
                $request->validate([
                    'blood_pressure' => [function ($field, $value, $fail) {
                        if (!is_null($value) && !preg_match('/^\d{2,3}\/\d{2,3}$/', $value)) {
                            return $fail("Invalid format for blood pressure.");
                        }
                    }],
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

                return redirect()->to(route('nurses.admissions.treatment-preview', $admission) . "?treatments=" . join(',', array_keys($request->ministered))); //->with('ministered', array_keys($request->ministered));
            }
        }

        $admission->load(['patient', 'ward', 'admittable', 'plan.user', 'tests', 'plan.treatments']);

        if (request()->user()->department_id == Department::DOC->value) {
            return view('doctors.admissions.show', ['data' => $admission]);
        }

        if (request()->user()->department_id  == Department::NUR->value) {
            return view('nursing.admissions.show', ['admission' => $admission]);
        }
    }

    public function edit(Request $request, Admission $admission)
    {
        $admission->plan->load(['tests', 'treatments']);

        if (!$request->isMethod('POST')) {
            return view('doctors.admissions.edit', compact('admission'));
        }

        dd($request->all());
    }

    public function showPlan(Request $request, Admission $admission)
    {
        return view('doctors.admissions.view-plan', ['data' => $admission]);
    }

    public function getAdmissions(Request $request)
    {
        $admissions = Admission::with(['patient', 'ward'])->whereIn('status', [Status::active->value, Status::pending->value]);

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
        $admission->save();

        return redirect()->route('nurses.admissions.get');
    }

    public function previewTreatment(Request $request, Admission $admission)
    {
        if (!$request->isMethod('POST')) {
            $ministered = explode(',', $request->query('treatments'));
            $treatments = $admission->plan->treatments()->whereIn('id', $ministered)->get();

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
        return view('doctors.admissions.start', ['visit' => $visit]);
    }

    public function discharge(Request $request, Admission $admission)
    {
        $request->validate([
            'discharge_summary' => 'required|string',
        ]);

        $admission->discharged_on = now();
        $admission->status = Status::closed->value;
        $admission->save();

        $ward = $admission->ward;
        if ($ward) {
            $ward->filled_beds = max(0, $ward->filled_beds - 1);
            $ward->save();
        }

        return redirect()->route('nurses.admissions.get');
    }
}
