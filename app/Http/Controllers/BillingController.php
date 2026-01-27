<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        return view('billing.index');
    }

    public function getPendingBills(Request $request)
    {
        $this->authorize('viewAny', Bill::class);
        return $this->dataTable($request, Visit::accessibleBy($request->user())->with(['patient.category', 'visit'])->whereIn('status', [Status::active, Status::completed])->latest(), [
            function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'ilike', "%$search%")
                        ->orWhere('card_number', 'ilike', "$search%")
                        ->orWhere('phone', 'ilike', "$search%");
                });
            }
        ]);
    }

    public function patientBills(Request $request, Patient $patient)
    {
        $this->authorize('view', $patient);
        $patient->load('visits');
        return view('billing.patient-bills', compact('patient'));
    }

    public function getVisitBill(Request $request, Visit $visit)
    {
        $this->authorize('view', $visit);
        $bill = $visit->bill;
        return view('billing.visit-bill', compact('visit', 'bill'));
    }

    public function listPatientBills(Request $request, Visit $visit)
    {
        $this->authorize('view', $visit);
        return view('billing.visit-bills', compact('visit'));
    }

    public function getPaymentForm(Request $request, Bill $bill)
    {
        $this->authorize('view', $bill);
        return view('billing.init-payment', compact('bill'));
    }

    public function deleteBill(Request $request, Bill $bill)
    {
        $this->authorize('delete', $bill);
        $bill->payments()->delete();
        $bill->update(['status' => Status::cancelled->value]);

        return response()->json([
            'bill' => $bill->refresh(),
            'status' => 'ok',
            'ok' => true,
        ]);
    }
}
