<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PatientsController extends Controller
{
    //
    public function index(Request $reuest) {
        return view('records.patients');
    }
}
