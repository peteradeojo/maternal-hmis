<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\AncVisit;
use App\Models\AntenatalProfile;
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

    public function getAncVisits(Request $request)
    {
        return $this->dataTable($request, AncVisit::with(['profile'])->doesntHave('tests')->latest(), [
            function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "{$search}%")->orWhere('card_number', 'like', "{$search}%");
                });
            },
        ]);
    }

    public function ancBooking(Request $request, AntenatalProfile $profile)
    {
        if ($request->method() !== 'POST') {
            $tests = AncVisit::testsList;
            return view('lab.anc-booking', compact('profile', 'tests'));
        }

        $request->validate([
            'tests' => 'required|array',
            'tests.*' => 'nullable|string',
            'completed' => 'nullable|in:1,on,true,yes',
        ]);

        $profile->tests = array_merge($profile->tests ?? [], $request->tests);
        if ($request->completed) $profile->awaiting_lab = false;
        $profile->save();

        return redirect()->route('lab.antenatals')->with('success', 'Tests booked successfully');
    }

    public function testAnc(Request $request, AncVisit $visit)
    {
        if ($request->method() !== 'POST') {
            $tests = array_diff(AncVisit::testsList, ['HIV', 'Hepatitis B', 'VDRL', 'Blood Group', 'Genotype', 'Pap Smear']);
            return view('lab.anc-test', compact('tests', 'visit'));
        }

        $visit->update([
            'edema' => $request->edema ?? '',
            'pcv' => $request->pcv ?? '',
            'protein' => $request->protein ?? '',
            'glucose' => $request->glucose,
        ]);

        // $visit->awaiting_doctor = true;
        // $visit->awaiting_lab_results = false;
        // $visit->save();

        return redirect()->route('dashboard');
    }

    public function testReport(Request $request, Documentation $doc)
    {
        return view('lab.testReport', compact('doc'));
    }
}
