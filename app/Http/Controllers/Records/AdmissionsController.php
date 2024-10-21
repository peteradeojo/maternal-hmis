<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdmissionsController extends Controller
{
    public function index()
    {
        return view('records.admissions');
    }
}
