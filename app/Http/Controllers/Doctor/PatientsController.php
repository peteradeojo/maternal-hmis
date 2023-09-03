<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;

class PatientsController extends Controller
{
    public function treat(Request $request, Visit $visit)
    {
        if ($request->method() !== 'POST') return view('doctors.consultation-form', compact('visit'));
    }
}
