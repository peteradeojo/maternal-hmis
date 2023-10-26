<?php

namespace App\Http\Controllers;

use App\Models\Documentation;
use App\Models\DocumentationPrescription;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        $data = Documentation::with(['patient'])->has('treatments')->get();
        return view('phm.prescriptions', compact('data'));
    }

    public function getPrescriptions(Request $request)
    {
        return $this->dataTable($request, DocumentationPrescription::query());
    }

    public function show(Request $request, Documentation $doc)
    {
        $doc->load(['treatments', 'patient']);

        return view('phm.show-prescription', compact('doc'));
    }
}
