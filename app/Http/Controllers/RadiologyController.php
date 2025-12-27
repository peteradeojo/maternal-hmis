<?php

namespace App\Http\Controllers;

use App\Enums\AppNotifications;
use App\Enums\Department as EnumsDepartment;
use App\Enums\Status;
use App\Jobs\UploadPatientScans;
use App\Models\PatientImaging;
use App\Models\Visit;
use Illuminate\Http\Request;

class RadiologyController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PatientImaging::class);
        return view('rad.scans', ['patientId' => $request->query('patient_id')]);
    }

    public function show(Request $request, PatientImaging $scan)
    {
        $this->authorize('view', $scan);
        $scan->load(['patient']);

        return view('rad.scan', ['doc' => $scan]);
    }

    public function getScans(Request $request)
    {
        $this->authorize('viewAny', PatientImaging::class);
        $visitId = $request->query('patient_id');
        $query = PatientImaging::accessibleBy($request->user())->with(['patient', 'requester'])->latest();

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
        $this->authorize('create', PatientImaging::class);
        $request->validate([
            'scan' => 'required|string',
        ]);

        $visit->visit->radios()->create([
            'patient_id' => $visit->patient_id,
            'requested_by' => $request->user()->id,
            'name' => $request->scan,
        ]);

        notifyDepartment(EnumsDepartment::RAD->value, [
            'title' => 'New Imaging Request',
            'message' => "Imaging request for {$visit->patient->name}",
        ], [
            'mode' => AppNotifications::$BOTH,
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }

    public function storeResult(Request $request, PatientImaging $scan)
    {
        $this->authorize('update', $scan);
        $scan->results = $request->except('_token');
        $scan->uploaded_by = $request->user()->id;
        $scan->uploaded_at = now();

        $scan->status = Status::completed->value;
        $scan->save();

        return response()->json($scan->refresh());
    }

    public function scanResult(Request $request, PatientImaging $scan)
    {
        $this->authorize('view', $scan);
        return response()->json([
            'path' => $scan->secure_path,
            'name' => $scan->name,
            'updated_at' => $scan->updated_at,
        ]);
    }

    public function history()
    {
        $this->authorize('viewAny', PatientImaging::class);
        // $history = PatientImaging::where('path', '!=', 'null')->orWhere('comment', '!=', null)->latest()->get();
        return view('rad.history');
    }

    public function getScansHistory(Request $request)
    {
        $this->authorize('viewAny', PatientImaging::class);
        return $this->dataTable($request, PatientImaging::accessibleBy($request->user())->with(['patient', 'requester'])->where('path', '!=', 'null')->orWhere('comment', '!=', null)->latest(), []);
    }

    public function getScanResult(Request $request, PatientImaging $scan)
    {
        $this->authorize('view', $scan);
        $results = $scan->results;

        if (!$results) {
            return response()->json()->status(404);
        }

        if ($results->report_type == 'obstetric') {
            return view('rad.results.obstetric', compact('scan'));
        }
        if ($results->report_type == 'general') {
            return view('rad.results.general', compact('scan'));
        }
        if ($results->report_type == 'echo') {
            return view('rad.results.echo', compact('scan'));
        }
    }
}
