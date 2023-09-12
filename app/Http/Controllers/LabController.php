<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Documentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabController extends Controller
{
    public function test(Request $request, Documentation $documentation)
    {
        if ($request->method() !== 'POST') {
            return view('lab.take-test', compact('documentation'));
        }

        $data = $request->validate([
            'completed' => 'array',
            'description' => 'array',
            'description.*.*' => 'required|string',
            'result' => 'array',
            'result.*.*' => 'required|string',
            'unit' => 'array',
            'unit.*.*' => 'nullable|string',
            'reference_range' => 'array',
            'reference_range.*.*' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);

        foreach ($documentation->tests as $i => $test) {
            if (isset($data['result'][$i])) {
                $results = [];
                $resultData = $data['result'][$i] ?? [];
                $descriptionData = $data['description'][$i] ?? [];
                $unitData = $data['unit'][$i] ?? [];
                $referenceRangeData = $data['reference_range'][$i] ?? [];

                foreach ($resultData as $j => $result) {
                    $results[] = [
                        'result' => $result,
                        'description' => $descriptionData[$j],
                        'unit' => $unitData[$j],
                        'reference_range' => $referenceRangeData[$j],
                    ];
                }

                DB::beginTransaction();
                try {
                    if (isset($data['completed'][$i])) {
                        $test->status = Status::completed->value;
                    }

                    $test->results = $results;
                    $test->tested_by = $request->user()->id;
                    $test->save();
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    logger()->emergency($th->getMessage());
                    return back()->with('error', 'An error occured while saving test results');
                }
            }
        }

        if ($documentation->tests()->where('status', Status::completed->value)->count() > 0) {
            $documentation->visit->awaiting_lab_results = false;
            $documentation->visit->awaiting_doctor = true;
            $documentation->visit->save();
        }

        return redirect()->route('dashboard')->with('success', 'Test results saved successfully');
    }

    public function history(Request $request)
    {
        return view('lab.history');
    }

    public function getHistory(Request $request)
    {
        return $this->dataTable($request, Documentation::whereHas('tests', function ($query) {
        }), [
            function (&$query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "{$search}%");
                });
            }
        ]);
    }
}
