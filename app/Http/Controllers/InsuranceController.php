<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\InsuranceProfiles;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    public function index(Request $request)
    {
        return view('nhi.patients');
    }

    public function getPatients(Request $request)
    {
        $query = Patient::has('insurance')->with(['category', 'insurance']);
        return $this->dataTable($request, $query);
    }

    public function showPatient(Request $request, Patient $patient)
    {
        $patient->load(['insurance', 'category']);

        return view('nhi.show-patient', compact('patient'));
    }

    public function encounters(Request $request)
    {
        return view('nhi.encounters');
    }

    public function showEncounter(Request $request, Visit $visit)
    {
        return view('nhi.show-encounter', compact('visit'));
    }

    public function cancelInsuranceProfile(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $profile = InsuranceProfiles::where('id', $request->input('id'))->update([
            'status' => Status::cancelled->value,
        ]);

        return response()->json([
            'ok' => true,
            'data' => $profile,
        ]);
    }

    public function editProfile(Request $request, InsuranceProfiles $profile)
    {
        if ($request->isMethod('GET')) {
            return view('nhi.edit-insurance', ['profile' => $profile]);
        }

        $data = $request->validate([
            'hmo_name' => 'required|string',
            'hmo_company' => 'required|string',
            'hmo_id_no' => 'required|string',
            'status' => 'nullable|integer',
            'validity_from' => 'nullable|date',
            'validity_to' => 'nullable|date',
        ]);

        $profile->update($data);

        return redirect()->back();
    }
}
