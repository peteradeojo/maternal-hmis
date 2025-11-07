<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use Illuminate\Http\Request;

class AdmissionsController extends Controller
{
    public function index()
    {
        $admissions = Admission::all();
        return view('records.admissions', compact('admissions'));
    }
}
