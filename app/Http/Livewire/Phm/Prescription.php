<?php

namespace App\Http\Livewire\Phm;

use App\Enums\Status;
use App\Models\Admission;
use App\Models\AdmissionPlan;
use App\Models\BillDetail;
use App\Models\DispenseLine;
use App\Models\Location;
use App\Models\Prescription as ModelsPrescription;
use App\Models\PrescriptionLine;
use App\Models\StockTransaction;
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
    public $dispensing = [];

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
            fn($a, $b) => $a + (($b['quantity'] + $b['dispensed']) * ($b['status'] == Status::blocked ? 0 : TreatmentService::getPrice(
                $b['item_id'],
                $b['profile'],
            ))),
            0
        );
    }

    public function loadLines()
    {
        $this->prescriptions = $this->doc->lines->map(function ($line) {
            $dispensed = $line->dispenses->sum('qty_dispensed');
            $quantity = $line->qty_dispensed ?? TreatmentService::getCount($line->item, $line);
            return [
                'id' => $line->id,
                'item_id' => $line->item_id,
                'name' => $line->item?->name ?? $line->description,
                'quantity' => $quantity,
                'profile' => $line->profile ?? 'RETAIL',
                'status' => $line->status,
                'balance' => $line->item?->balance ?? 0,
                'dosage' => $line->dosage,
                'price' => TreatmentService::getPrice($line->item_id, $line->profile) ?? 0,
                'frequency' => $line->frequency,
                'duration' => $line->duration,
                'weight' => $line->item?->weight,
                'si_unit' => $line->item?->si_unit,
                'dispensed' => $dispensed,
                'total_qty' => $dispensed + $quantity,
            ];
        })->toArray();

        if ($this->doc->event instanceof Visit) {
            $this->bill = $this->doc->event->bills()->whereIn('status', [Status::pending->value, Status::active->value])->first();
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
            report($th);
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
        if ($this->prescriptions[$i]['status'] == Status::completed) return;

        if ($checked) {
            $this->prescriptions[$i]['status'] = Status::active;
        } else {
            $this->prescriptions[$i]['status'] = Status::blocked;
        }

        $this->compute();
    }

    public function saveToBill()
    {
        $event = null;
        if ($this->doc->event instanceof AdmissionPlan == false) {
            $event = ($this->doc->event->load(['bills']));
        }
        DB::beginTransaction();

        try {
            $bill = $event?->bills->where('status', Status::pending->value)->first();


            foreach ($this->prescriptions as $i => $line) {
                if ($line['status'] == Status::completed) continue;

                $price = TreatmentService::getPrice(@$line['item_id'], $line['profile']);

                PrescriptionLine::where('id', $line['id'])->update([
                    'status' => $line['status'],
                    'profile' => $line['profile'],
                    'qty_dispensed' => $line['quantity'],
                ]);

                if (!empty($bill)) {
                    BillDetail::updateOrCreate([
                        'chargeable_type' => PrescriptionLine::class,
                        'chargeable_id' => $line['id'],
                        'bill_id' => $bill->id,
                    ], [
                        'user_id' => auth()->user()->id,
                        'description' => "{$line['name']} {$line['dosage']} {$line['frequency']} for {$line['duration']} days(s)",
                        'quantity' => floatval($line['quantity'] ??  TreatmentService::getCount($line, (object) $line)) + $line['dispensed'],
                        'unit_price' => $price,
                        'total_price' => $price * ($line['quantity'] + $line['dispensed']),
                        'status' => $line['status']->value,
                        'tag' => 'drug',
                    ]);
                }
            }

            DB::commit();
            notifyUserSuccess("Bill saved for patient {$this->doc->patient->name}", auth()->user()->id);
        } catch (\Throwable $th) {
            report($th);
            DB::rollBack();
            notifyUserError($th->getMessage(), auth()->user()->id);
        }
    }

    public function dispense()
    {
        $this->saveToBill();

        $this->reset('dispensing');

        foreach ($this->prescriptions as $line) {
            if ($line['status'] != Status::active) continue;
            if (empty($line['item_id'])) continue;

            $pLine = PrescriptionLine::find($line['id']);
            $this->dispensing[] = $pLine->getDispensingReport();
        }

        $this->dispatch('open-dispense-confirm');
    }

    public function confirmDispense($andClose = false) {
        DB::beginTransaction();
        $userId = auth()->user()->id;

        try {
            foreach ($this->dispensing as $d) {
                // dump($d);continue;
                DispenseLine::create([
                    'source_type' => PrescriptionLine::class,
                    'source_id' => $d['id'],
                    'user_id' => $userId,
                    'qty_dispensed' => $d['quantity'],
                ]);

                StockTransaction::create([
                    'tx_type' => StockTransaction::ISSUE,
                    'item_id' => $d['item_id'],
                    'quantity' => $d['quantity'],
                    'unit' => $d['unit'],
                    'unit_cost' => $d['price'],
                    'from_location_id' => Location::STORE,
                    'to_location_id' => Location::OUTBOUND,
                    'reason' => "Dispensed to patient",
                    'performed_by' => $userId,
                ]);

                Prescription::where('id', $d['id'])->first()?->update(['qty_dispensed', 0]);
            }

            if ($andClose) {
                $this->doc->update([
                    'status' => Status::closed,
                ]);
            }

            DB::commit();
            notifyUserSuccess("Prescriptions have been dispensed.", $userId);

            $this->reset('dispensing');
            $this->loadLines();
            $this->compute();

            $this->dispatch("close-dispense-confirm");
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);

            notifyUserError($th->getMessage(), $userId);
            $this->dispatch("close-dispense-confirm");
        }
    }
}
