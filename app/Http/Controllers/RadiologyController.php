<?php

namespace App\Http\Controllers;

use App\Models\PatientImaging;
use Illuminate\Http\Request;

class RadiologyController extends Controller
{
    public function index(Request $request)
    {
        return view('rad.scans');
    }

    public function show(Request $request, PatientImaging $img)
    {
        dd($img);
    }

    public function getScans(Request $request)
    {
        $query = PatientImaging::with(['patient', 'requester']);
        return $this->dataTable($request, $query, []);
    }
}
