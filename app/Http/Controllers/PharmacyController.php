<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Documentation;
use App\Models\DocumentationPrescription;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        $data = Documentation::with(['patient'])->whereHas('treatments', fn ($q) => $q->whereIn('status', [Status::pending->value, Status::quoted->value]))->get();
        return view('phm.prescriptions', compact('data'));
    }

    public function getPrescriptions(Request $request)
    {
        return $this->dataTable($request, DocumentationPrescription::query());
    }

    public function show(Request $request, Documentation $doc)
    {
        $doc->load(['treatments', 'patient']);
        return view('phm.show-prescription', compact('doc'));
    }

    public function dispensaryIndex(Request $request)
    {
        // $data = Visit::with(['patient'])->whereHas('visit', function ($query) {
        //     $query->whereHas('treatments', fn ($q) => $q->whereIn('status', [Status::quoted->value, Status::closed->value, Status::completed->value]));
        // })->get();
        $data  = Visit::with(['patient', 'visit'])->whereHas('visit', function ($q) {
            $q->has('treatments');
        })->get();
        return view('phm.prescriptions', compact('data'));
    }

    public function dispensaryShow(Request $request, Visit $visit)
    {
        $doc = $visit->visit;
        if ($request->method() == 'POST') {
            $request->mergeIfMissing(['available' => []]);

            $amount = $request->amount;
            $available = $request->available;

            DB::beginTransaction();

            $available_ids = array_keys($available);

            try {
                foreach ($amount as $i => $amt) {
                    if ($amt) $doc->treatments()->where('id', $i)->update(['amount' => $amt]);
                }
                $doc->treatments()->whereIn('id', $available_ids)->update(['available' => true]);
                $doc->treatments()->whereNotIn('id', $available_ids)->update(['available' => false]);

                if ($request->has('complete')) {
                    $doc->treatments()->update(['status' => Status::quoted->value]);
                    DB::commit();
                    return redirect()->route('dis.index');
                } else {
                    $doc->treatments()->whereIn('status', [Status::quoted->value])->update(['status' => Status::pending->value]);
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
            }
            return redirect()->back();
        }
        $doc->load(['patient', 'treatments']);
        return view('dis.show-prescription', compact('doc'));
    }

    public function closePrescription(Request $request, Documentation $doc)
    {
        $doc->treatments()->update(['status' => Status::completed->value]);
        return redirect()->route('phm.prescriptions');
    }
}
