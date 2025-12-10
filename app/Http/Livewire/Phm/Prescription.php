<?php

namespace App\Http\Livewire\Phm;

use App\Enums\Status;
use App\Models\Admission;
use App\Models\AdmissionPlan;
use App\Models\Bill;
use App\Models\Prescription as ModelsPrescription;
use App\Models\PrescriptionLine;
use App\Models\Visit;
use App\Services\TreatmentService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Prescription extends Component
{
    public ModelsPrescription $doc;

    public $prescriptions = [];
    public $totalAmt = 0;

    public $prices = [];

    public $bill = null;

    public $is_admission = false;

    public function mount(ModelsPrescription $doc)
    {
        $this->doc = $doc;
        $this->doc->load(['lines.item']);

        $this->prices = $this->doc->lines->map(fn($line) => $line->prices);

        $this->loadLines();

        $this->is_admission = ($this->doc->event instanceof Admission);
        $this->compute();
    }

    public function reload() {}

    public function render()
    {
        return view('livewire.phm.prescription');
    }

    public function getPrices($id)
    {
        return $this->doc->lines->where('id', $id)->first()->item->prices();
    }

    public function compute()
    {
        $this->totalAmt = array_reduce(
            $this->prescriptions,
            fn($a, $b) => $a + ($b['quantity'] * ($b['status'] == Status::cancelled ? 0 : TreatmentService::getPrice(
                $b['item_id'],
                $b['profile'],
            ))),
            0
        );
    }

    public function loadLines()
    {
        $this->prescriptions = $this->doc->lines->map(function ($line) {
            return [
                'id' => $line->id,
                'item_id' => $line->item_id,
                'name' => $line->item?->name ?? $line->description,
                'quantity' => TreatmentService::getCount($line->item, $line),
                'profile' => $line->profile ?? 'RETAIL',
                'status' => $line->status,
                'balance' => $line->item->balance,
                'dosage' => $line->dosage,
                'frequency' => $line->frequency,
                'duration' => $line->duration,
            ];
        })->toArray();

        if ($this->doc->event instanceof Visit) {
            $this->bill = $this->doc->event->bills()->where('status', Status::pending->value)->first();
        }
    }

    public function addLine($data)
    {
        try {
            $this->doc->lines()->create([
                'item_id' => $data['productId'],
                'dosage' => $data['dosage'],
                'frequency' => $data['frequency'],
                'duration' => $data['duration'],
                'prescribed_by' => auth()->user()->id,
                'status' => Status::active,
                'profile' => 'RETAIL',
            ]);
        } catch (\Throwable $th) {
            notifyUserError("Unable to add prescription", auth()->user());
        }

        $this->loadLines();
        $this->compute();
    }

    public function updateProfile($index, $profile)
    {
        $this->prescriptions[$index]['profile'] = $profile;
        $this->compute();
    }

    public function setLineStatus($i, $checked)
    {
        if ($checked) {
            $this->prescriptions[$i]['status'] = Status::active;
        } else {
            $this->prescriptions[$i]['status'] = Status::blocked;
        }

        $this->compute();
    }

    public function saveToBill()
    {
        $event = ($this->doc->event->load(['bills']));

        DB::beginTransaction();

        try {
            $bill = $event->bills()->where('status', Status::pending->value)->first();

            if (empty($bill)) {
                $bill = $event->bills()->create([
                    'bill_number' => Bill::generateBillNumber($event),
                    'status' => Status::pending->value,
                    'created_by' => auth()->user()->id,
                    'patient_id' => $this->doc->patient_id,
                    'bill_date' => now(),
                ]);
            }

            foreach ($this->prescriptions as $i => $line) {
                $price = TreatmentService::getPrice($line['item_id'], $line['profile']);

                PrescriptionLine::where('id', $line['id'])->update([
                    'status' => $line['status'],
                    'profile' => $line['profile'],
                ]);

                $bill->entries()->updateOrCreate([
                    'chargeable_type' => PrescriptionLine::class,
                    'chargeable_id' => $line['id'],
                    'bill_id' => $bill->id,
                ], [
                    'user_id' => auth()->user()->id,
                    'description' => "{$line['name']} {$line['dosage']} {$line['frequency']} for {$line['duration']} days(s)",
                    'quantity' => $line['quantity'],
                    'unit_price' => $price,
                    'total_price' => $price * $line['quantity'],
                    'status' => $line['status']->value,
                    'tag' => 'drug',
                ]);
            }

            DB::commit();

            notifyUserSuccess("Bill saved for patient {$this->doc->patient->name}", auth()->user()->id);
        } catch (\Throwable $th) {
            report($th);
            DB::rollBack();
            notifyUserError($th->getMessage(), auth()->user()->id);
        }
    }
}
