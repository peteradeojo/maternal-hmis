<?php

namespace App\Http\Controllers;

use App\Models\Documentation;
use App\Models\PatientImaging;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RadiologyController extends Controller
{
    public function index(Request $request)
    {
        return view('rad.scans');
    }

    public function show(Request $request, Documentation $doc)
    {
        $doc->load(['radios', 'patient']);

        return view('rad.scan', compact('doc'));
    }

    public function getScans(Request $request)
    {
        $query = DB::table('patient_imagings', 'pi')->selectRaw("GROUP_CONCAT(pi.name SEPARATOR ',') scans, d.created_at, d.id, p.name")
            ->leftJoin(DB::raw("documentations d"), "d.id", "=", 'pi.documentation_id')
            ->leftJoin(DB::raw("patients p"), "p.id", "=", "d.patient_id")
            ->groupBy('d.created_at', 'd.id');
        return $this->dataTable($request, $query, []);
    }
}
