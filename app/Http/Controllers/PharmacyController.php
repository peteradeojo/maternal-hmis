<?php

namespace App\Http\Controllers;

use App\Enums\EventLookup;
use App\Enums\Status;
use App\Models\Admission;
use App\Models\AdmissionPlan;
use App\Models\Bill;
use App\Models\Documentation;
use App\Models\Prescription;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        return view('phm.prescriptions');
    }

    public function getPrescriptions(Request $request)
    {
        $query = Prescription::with(['patient'])->whereHasMorph('event', [Visit::class])->where('status', Status::active)->latest();

        return $this->dataTable($request, $query, [
            function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'ilike', "%$search%")->orWhere('phone', 'ilike', "$search%");
                });
            }
        ]);
    }

    public function show(Request $request, Documentation $doc)
    {
        $doc->load(['treatments', 'patient']);
        return view('phm.show-prescription', compact('doc'));
    }

    public function dispensaryIndex(Request $request)
    {
        return view('phm.prescriptions');
    }

    public function dispensaryShow(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type');

        $doc = EventLookup::fromName($type)->value::findOrFail($id)->load('treatments');
        return view('dis.show-prescription', compact('doc', 'type', 'id'));
    }

    public function closePrescription(Request $request, Documentation $doc)
    {
        $doc->treatments()->update(['status' => Status::completed->value]);
        return redirect()->route('phm.prescriptions');
    }

    public function getBill(Request $request, Bill $bill)
    {
        $bill->load(['entries']);
        return view('dis.bill', compact('bill'));
    }

    public function viewPrescription(Request $request, Prescription $prescription)
    {
        return view('phm.show-prescription', compact('prescription'));
    }

    public function admissions(Request $request)
    {
        $admissions = Admission::valid()->latest()->get();
        return view('phm.admissions.index', compact('admissions'));
    }

    public function showAdmissionTreatment(Request $request, Admission $admission)
    {
        $prescription = $admission->plan->prescription()->firstOrCreate([
            'patient_id' => $admission->patient_id,
        ]);

        return view('phm.show-prescription', compact('prescription'));
    }
}
