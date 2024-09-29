<?php

namespace App\Http\Controllers;

use App\Enums\Department as EnumsDepartment;
use App\Jobs\UploadPatientScans;
use App\Models\Department;
use App\Models\Documentation;
use App\Models\PatientImaging;
use App\Models\Visit;
use App\Notifications\StaffNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RadiologyController extends Controller
{
    public function index(Request $request)
    {
        return view('rad.scans', ['patientId' => $request->query('patient_id')]);
    }

    public function show(Request $request, PatientImaging $doc)
    {
        $doc->load(['patient']);

        return view('rad.scan', compact('doc'));
    }

    public function getScans(Request $request)
    {
        // $query = DB::table('patient_imagings', 'pi')->selectRaw("GROUP_CONCAT(pi.name SEPARATOR ',') scans, d.created_at, d.id, p.name")
        //     ->leftJoin(DB::raw("documentations d"), "d.id", "=", 'pi.documentation_id')
        //     ->leftJoin(DB::raw("patients p"), "p.id", "=", "d.patient_id")
        //     ->groupBy('d.created_at', 'd.id');

        $visitId = $request->query('patient_id');
        $query = PatientImaging::query()->with(['patient', 'requester'])->where('path', null);

        return $this->dataTable($request, $query, [
            function ($query, $search) use ($visitId) {
                if ($visitId) {
                    $query->where('patient_id', $visitId);
                } else {
                    $query->where(function ($query) use (&$search, &$visitId) {
                        $query->whereHas('patient', function ($q) use (&$search) {
                            $q->where('name', 'like', "%$search%");
                        });
                    });
                }
            },
        ]);
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

    public function storeResult(Request $request, PatientImaging $scan)
    {
        $request->validate([
            'result_file' => 'nullable|file|mimes:png,jpg,pdf,docx',
            'comment' => 'nullable|string|required_without:result_file',
        ]);

        $file = $request->file('result_file');

        if ($file) {
            $filepath = $file->store('radiology');
            $scan->path = $filepath;

            dispatch(new UploadPatientScans($scan))->delay(30);
        }

        $scan->comment ??= $request->comment;
        $scan->uploaded_by = auth()->user()->id;
        $scan->save();

        return back();
    }

    public function scanResult(Request $request, PatientImaging $scan)
    {
        return response()->json([
            'path' => $scan->secure_path,
            'name' => $scan->name,
            'updated_at' => $scan->updated_at,
        ]);
    }
}
