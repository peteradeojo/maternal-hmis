<?php

namespace App\Http\Controllers;

use App\Enums\AppNotifications;
use App\Enums\Department as EnumsDepartment;
use App\Enums\Status;
use App\Models\Admission;
use App\Models\AncVisit;
use App\Models\AntenatalProfile;
use App\Models\DocumentationTest;
use App\Models\GeneralVisit;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabController extends Controller
{
    private function processTests(Request $request, $data, GeneralVisit|AncVisit  $visit)
    {
        foreach ($visit->tests as $i => $test) {
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

                if (isset($data['completed'][$i])) {
                    $test->status = Status::completed->value;
                }

                $test->results = $results;
                $test->tested_by = $request->user()->id;
                $test->save();
            }
        }
    }

    public function viewPatientTests(Request $request, Patient $patient)
    {
        $tests = DocumentationTest::where('patient_id', $patient->id)
            ->where('status', '!=', Status::cancelled->value)
            ->latest()->get();
        return view('lab.take-tests', compact('tests', 'patient'));
    }

    // public function test(Request $request, $visit)
    // {
    //     if ($request->method() !== 'POST') {
    //         dd($visit);
    //         return view('lab.take-test', ['documentation' => $test->testable]);
    //     }

    //     $data = $request->validate([
    //         'completed' => 'array',
    //         'description' => 'array',
    //         'description.*.*' => 'required|string',
    //         'result' => 'array',
    //         'result.*.*' => 'required|string',
    //         'unit' => 'array',
    //         'unit.*.*' => 'nullable|string',
    //         'reference_range' => 'array',
    //         'reference_range.*.*' => 'nullable|string',
    //         'comment' => 'nullable|string',
    //     ]);

    //     foreach ($visit->visit->tests as $i => $test) {
    //         if (isset($data['result'][$i])) {
    //             $results = [];
    //             $resultData = $data['result'][$i] ?? [];
    //             $descriptionData = $data['description'][$i] ?? [];
    //             $unitData = $data['unit'][$i] ?? [];
    //             $referenceRangeData = $data['reference_range'][$i] ?? [];

    //             foreach ($resultData as $j => $result) {
    //                 $results[] = [
    //                     'result' => $result,
    //                     'description' => $descriptionData[$j],
    //                     'unit' => $unitData[$j],
    //                     'reference_range' => $referenceRangeData[$j],
    //                 ];
    //             }

    //             DB::beginTransaction();
    //             try {
    //                 if (isset($data['completed'][$i])) {
    //                     $test->status = Status::completed->value;
    //                 }

    //                 $test->results = $results;
    //                 $test->tested_by = $request->user()->id;
    //                 $test->save();
    //                 DB::commit();
    //             } catch (\Throwable $th) {
    //                 DB::rollBack();
    //                 logger()->emergency($th->getMessage());
    //                 return back()->with('error', 'An error occured while saving test results');
    //             }
    //         }
    //     }

    //     if ($visit->visit->tests()->where('status', Status::completed->value)->count() > 0) {
    //         $visit->awaiting_lab_results = false;
    //     }

    //     $visit->awaiting_doctor = true;
    //     $visit->save();

    //     return redirect()->route('dashboard')->with('success', 'Test results saved successfully');
    // }

    public function history(Request $request)
    {
        return view('lab.history');
    }

    public function getHistory(Request $request)
    {
        $Q = DocumentationTest::selectRaw('testable_type, testable_id, patient_id, MAX(created_at) created_at')->groupBy('testable_type', 'testable_id', 'patient_id', 'created_at')->where(function ($q) {
            $q->where('status', Status::completed->value)->orWhere(function ($query) {
                $query->whereNotNull('results');
            });
        })->with(['patient', 'testable.visit']);
        // return $this->dataTable($request, Visit::with(['patient'])->whereHas('visit', function ($query) {
        return $this->dataTable($request, $Q, [
            function (&$query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "{$search}%");
                });
            }
        ]);
    }

    public function getAncVisits(Request $request)
    {
        return $this->dataTable($request, AncVisit::with(['profile'])->whereHas('tests', function ($q) {
            $q->where('status', '!=', Status::completed->value);
        })->orWhereHas('profile', function ($q) {
            $q->whereHas('tests', function ($q) {
                $q->where('status', '!=', Status::completed->value);
            });
        })->latest(), [
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
            $visit->load(['tests', 'treatments']);
            $tests = array_diff(AncVisit::testsList, ['HIV', 'Hepatitis B', 'VDRL', 'Blood Group', 'Genotype', 'Pap Smear']);
            return view('lab.take-test', compact('tests', 'visit') + ['documentation' => $visit]);
        }

        $visit->visit->awaiting_doctor = true;
        $visit->visit->save();

        $this->processTests($request, $request->all(), $visit);

        return redirect()->route('dashboard');
    }

    public function testReport(Request $request, Patient $patient)
    {
        $tests = DocumentationTest::with(['patient'])->where('patient_id', $patient->id)->latest()->get();
        return view('lab.testReport', compact('tests', 'patient'));
    }

    public function store(Request  $request, Visit $visit)
    {
        $request->validate([
            'test' => 'required|string'
        ]);

        $visit->visit->tests()->create([
            'name' => $request->test,
            'patient_id' => $visit->patient_id,
            'user_id' =>  $request->user()->id,
        ]);

        notifyDepartment(EnumsDepartment::LAB->value, [
            'title' => 'New Lab Test Requested',
            'message' => "New test requested for {$visit->patient->name}",
        ], [
            'mode' => AppNotifications::$BOTH,
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }

    public function admissions()
    {
        $adm = Admission::latest()->get();
        return view('lab.admissions', compact('adm'));
    }

    public function saveTest(Request $request, DocumentationTest $test)
    {
        $request->validate([
            'results' => 'nullable|array',
            'results.*' => 'array',
            'results.*.description' => 'string',
            'results.*.result' => 'string|nullable',
            'results.*.unit' => 'string|nullable',
            'results.*.reference_range' => 'string|nullable',
            'status' => 'integer|nullable',
        ]);

        $test->results = $request->input('results');
        $test->status = $request->input('status'); // == true ? Status::completed->value : Status::pending->value;
        $test->tested_by = auth()->user()->id;

        $test->save();

        return response()->json(['test' => $test]);
    }

    public function viewTests(Request $request, Visit $visit)
    {
        return view('lab.tests', compact('visit'));
    }

    public function getTests(Request $request)
    {
        $query = Visit::with(['patient.category', 'visit'])->has('tests')->orWhereHas('visit', function ($query) {
            $query->has('tests');
        })->latest()->orderBy('status');
        return $this->dataTable($request, $query, [
            function ($query, $search) {
                $query->whereHas('patient', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('card_number', 'like', "$search%")
                        ->orWhere('phone', 'like', "$search%");
                });
            }
        ]);
    }
}
