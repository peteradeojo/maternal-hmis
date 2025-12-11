<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdmissionsController extends Controller
{
    public function index(Request $request) {
        return view('phm.admissions.index');
    }
}
