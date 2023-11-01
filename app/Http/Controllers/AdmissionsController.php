<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Admission;
use App\Models\Ward;
use Illuminate\Http\Request;

class AdmissionsController extends Controller
{
    public function index(Request $request)
    {
        return view('nursing.admissions');
    }

    public function show(Request  $request, Admission $admission)
    {
        $admission->load(['patient', 'ward', 'admittable']);

        return view('nursing.show-admission', ['admission' => $admission]);
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
            $wards = Ward::whereRaw('filled_beds < beds')->get();
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

        $admission->ward_id = $ward->id;
        $admission->save();

        return redirect()->route('nurses.admissions.get');
    }
}
