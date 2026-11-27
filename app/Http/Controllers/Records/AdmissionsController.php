<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use Illuminate\Http\Request;

class AdmissionsController extends Controller
{
    public function index()
    {
        /* $admissions = Admission::valid()->latest()->get(); */

        return view('records.admissions');
    }

    public function getAdmissions(Request $request)
    {
        $query = Admission::valid()->with(['patient', 'ward'])->latest();

        return $this->dataTable($request, $query, [
            function ($query, $search) {
                $query->whereHas('patient', function ($q) use (&$search) {
                    $q->where('name', 'ilike', "%$search%");
                });
            },
        ]);
    }

    public function show(Request $request, Admission $admission)
    {
        if ($request->has('sparse')) {
            return view('admissions.show-sparse', compact('admission'));
        }

        return view('admissions.show', []);
    }
}
