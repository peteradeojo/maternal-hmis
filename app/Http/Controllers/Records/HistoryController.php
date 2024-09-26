<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        // dd(Visit::with(['patient'])->get());
        return view('records.history');
    }

    public function getHistory(Request $request)
    {
        return $this->dataTable($request, Visit::with(['patient.category', 'visit'])->latest(),  [
            function ($query, $search) {
                return $query->whereHas('patient', function ($q) use (&$search) {
                    return $q->where('name', 'like', "$search%")->orWhere('card_number',  'like',  "$search%");
                });
            },
        ]);
    }

    public function show(Request $request, Visit $visit)
    {
        $visit->load(['visit', 'patient', 'visit.tests', 'visit.radios', 'visit.prescriptions']);
        $patient = $visit->patient;
        return view('records.show-history', ['visit' => $visit->visit, 'patient' => $patient]);
    }
}
