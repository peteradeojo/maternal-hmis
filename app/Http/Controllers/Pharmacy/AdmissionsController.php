<?php

namespace App\Http\Controllers\Pharmacy;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Admission;
use Illuminate\Http\Request;

class AdmissionsController extends Controller
{
    public function index(Request $request) {
        $admissions = Admission::with(['patient', 'ward'])->where('status', Status::active->value)->whereNot('ward_id', null)->get();
        return view('phm.admissions.index', compact('admissions'));
    }
}
