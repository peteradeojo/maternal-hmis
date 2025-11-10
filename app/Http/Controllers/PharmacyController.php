<?php

namespace App\Http\Controllers;

use App\Enums\EventLookup;
use App\Enums\Status;
use App\Models\Bill;
use App\Models\Documentation;
use App\Models\DocumentationPrescription;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        $data = Documentation::with(['patient'])->whereHas('treatments', fn($q) => $q->whereIn('status', [Status::pending->value, Status::quoted->value, Status::PAID->value]))->get();
        return view('phm.prescriptions', compact('data'));
    }

    public function getPrescriptions(Request $request)
    {
        // $query = DocumentationPrescription::query()->groupBy('event_type', 'event_id', 'patient_id')
        //     ->selectRaw('event_type, event_id, COUNT(*) as total, patient_id,
        //     MAX(created_at) created_at,
        //     SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_count', [Status::pending->value])
        //     ->with(['patient'])
        //     ->havingRaw('SUM(CASE WHEN status IN (?, ?, ?) THEN 1 ELSE 0 END) > 0', [Status::pending->value, Status::quoted->value, Status::blocked->value])
        //     ->orderByDesc('created_at')
        //     ->where("event_type", '!=', "");

        $query = Bill::with(['patient'])->whereHasMorph('billable', [Visit::class], function ($query) {
            $query->whereIn('status', [Status::active->value, Status::quoted->value, Status::pending->value])->has('treatments');
        })->whereHas('entries', function (Builder $query) {
            $query->where('tag', 'drug');
        })->whereIn('status', [Status::pending->value, Status::quoted->value])->latest();

        return $this->dataTable($request, $query, [
            function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")->orWhere('phone', 'like', "$search%");
                });
            }
        ]);
    }

    public function show(Request $request, Documentation $doc)
    {
        $doc->load(['treatments', 'patient']);
        return view('phm.show-prescription', compact('doc'));
    }

    public function dispensaryIndex(Request $request)
    {
        return view('phm.prescriptions');
    }

    public function dispensaryShow(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type');

        $doc = EventLookup::fromName($type)->value::findOrFail($id)->load('treatments');
        return view('dis.show-prescription', compact('doc', 'type', 'id'));
    }

    public function closePrescription(Request $request, Documentation $doc)
    {
        $doc->treatments()->update(['status' => Status::completed->value]);
        return redirect()->route('phm.prescriptions');
    }

    public function getBill(Request $request, Bill $bill)
    {
        $bill->load(['entries']);
        return view('dis.bill', compact('bill'));
    }
}
