<?php

namespace App\Http\Controllers;

use App\Enums\Department as EnumsDepartment;
use App\Models\Department;
use App\Models\Documentation;
use App\Models\PatientImaging;
use App\Models\Visit;
use App\Notifications\StaffNotification;
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

    public function store(Request $request, Visit $visit)
    {
        $request->validate([
            'scan' => 'required|string',
        ]);

        $visit->visit->radios()->create([
            'patient_id' => $visit->patient_id,
            'requested_by' => $request->user()->id,
            'name' => $request->scan,
        ]);

        $dept = Department::where('id', EnumsDepartment::RAD->value)->first();
        $dept->notifyParticipants(new StaffNotification("Imaging request for {$visit->patient->name}"));

        return response()->json([
            'ok' => true,
        ]);
    }
}
