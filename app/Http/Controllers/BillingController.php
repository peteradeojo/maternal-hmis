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
        return $this->dataTable($request, Visit::with(['patient', 'visit'])->whereIn('status', [Status::active, Status::completed])->latest(), [
            function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('card_number', 'like', "$search%")
                        ->orWhere('phone', 'like', "$search%");
                });
            }
        ]);
    }

    public function patientBills(Request $request, Patient $patient)
    {
        $patient->load('visits');
        return view('billing.patient-bills', compact('patient'));
    }

    public function getVisitBill(Request $request, Visit $visit)
    {
        $bill = $visit->bill;
        return view('billing.visit-bill', compact('visit', 'bill'));
    }

    public function listPatientBills(Request $request, Visit $visit)
    {
        return view('billing.visit-bills', compact('visit'));
    }

    public function getPaymentForm(Request $request, Bill $bill)
    {
        return view('billing.init-payment', compact('bill'));
    }

    public function deleteBill(Request $request, Bill $bill)
    {
        $bill->payments()->delete();
        $bill->update(['status' => Status::cancelled->value]);

        return response()->json([
            'bill' => $bill->refresh(),
            'status' => 'ok',
            'ok' => true,
        ]);
    }
}
