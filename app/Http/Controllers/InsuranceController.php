<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\InsuranceOrganization;
use App\Models\InsuranceProfiles;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            $orgs = InsuranceOrganization::all();
            return view('nhi.edit-insurance', ['profile' => $profile, 'orgs' => $orgs]);
        }

        $data = $request->validate([
            'orgid' => 'required|integer',
            'hmo_company' => 'required|string',
            'hmo_id_no' => 'required|string',
            'status' => 'nullable|integer',
            'validity_from' => 'nullable|date',
            'validity_to' => 'nullable|date',
        ]);

        $profile->update($data);

        return redirect()->back();
    }

    public function getOrganizations(Request $request)
    {
        $orgs = InsuranceOrganization::all();

        if ($request->expectsJson()) {
            return response()->json($orgs);
        }

        return Inertia::render('NHI/Organizations', [
            'orgs' => $orgs,
        ]);
    }

    public function createOrganization(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'contact_details' => 'nullable|array',
            'portal_url' => 'nullable|string',
            'contact_details.email' => 'nullable|string',
            'contact_details.phone' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        InsuranceOrganization::create($data);
        return to_route('nhi.orgs.index');
    }

    public function showOrganization(Request $request, InsuranceOrganization $org)
    {
        return Inertia::render('NHI/ShowOrganization', compact('org'));
    }

    public function editOrganization(Request $request, InsuranceOrganization $org)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'contact_details' => 'nullable|array',
            'portal_url' => 'nullable|string',
            'contact_details.email' => 'nullable|string',
            'contact_details.phone' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $org->update($data);
        return to_route('nhi.orgs.index');
    }
}
