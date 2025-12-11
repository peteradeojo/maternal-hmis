<?php

namespace App\Http\Livewire\Records;

use App\Enums\Department;
use App\Models\Bill;
use App\Enums\Status;
use App\Interfaces\OperationalEvent;
use App\Models\DocumentationPrescription;
use App\Models\PrescriptionLine;
use App\Models\Product;
use App\Models\StockItem;
use App\Services\TreatmentService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class BillReport extends Component
{
    public $visit;
    public $grandTotal = 0;

    public $items;

    public $others_amt = 0;

    public $tests;
    public $tests_amt;
    public $drugs;
    public $drugs_amt;
    public $scans;
    public $scans_amt;
    public $others = [];
    public $otherAmt = 0;

    public function mount($visit)
    {
        $this->visit = $visit;

        $this->drugs = $this->tests = $this->scans = [];

        $this->loadBillData($this->visit->admission);
        $this->loadBillData($this->visit->admission?->plan);
        $this->loadBillData($this->visit->visit);
        $this->loadBillData($this->visit);

        $this->others = [];
    }

    public function loadBillData(?OperationalEvent $evt)
    {
        if (empty($evt)) return;

        $drugs = $evt->prescription?->lines ?? collect([]);
        $tests = $evt->valid_tests;
        $scans = $evt->radios;

        $drugs = $drugs->map(function ($line) {
            $dispensed = $line->dispensed();

            return [
                'saved' => true,
                'product' => $line->item?->load(['prices'])->toArray(),
                'data' => $line->toArray(),
                'unit_price' => (TreatmentService::getPrice($line->item_id, $line->profile ?? 'RETAIL')),
                'total_amt' => ($dispensed + ($line['qty_dispensed'] ?? TreatmentService::getCount($line->item?->toArray(), $line))) * (TreatmentService::getPrice($line->item_id, $line->profile ?? 'RETAIL')),
            ];
        })->toArray();

        $tests = $tests->map(fn($test) => [
            'saved' => true,
            'product' => $test->describable->toArray(),
            'data' => $test->toArray()
        ])->toArray();

        $scans = $this->visit->imagings->load('describable')->map(fn($item) => [
            'saved' => true,
            'product' => $item->describable->toArray(),
            'data' => $item->toArray(),
        ])->toArray();

        $this->drugs = array_merge($this->drugs, $drugs);
        $this->scans = array_merge($this->scans, $scans);
        $this->tests = array_merge($this->tests, $tests);
    }

    public function render()
    {
        return view('livewire.records.bill-report');
    }

    public function addItem($id, $prop)
    {
        $pdt = Product::find($id);
        $comp = ['saved' => true, 'data' => null, 'product' => $pdt->toArray()];
        $this->{$prop}[] = $comp;
        $this->subTotal($prop);
    }

    public function addNewItem($name)
    {
        $this->others[] = [
            'name' => $name,
            'amount' => 0,
            'id' => null,
            'product' => [
                'amount' => 0,
                'name' => $name,
            ],
            'saved' => false,
        ];

        $this->subTotal('others');
    }

    public function subTotal($prop)
    {
        $this->{$prop . "_amt"} = collect($this->{$prop})->reduce(fn($a, $p) => $a + ($p['total_amt'] ?? $p['product']['amount']), 0);
    }

    public function removeItem($index, $prop)
    {
        // if ($index == 0) {
        //     array_shift($this->{$prop});
        //     return;
        // }

        // if ($index == count($this->{$prop}) - 1) {
        //     return array_pop($this->{$prop});
        // }

        // $this->{$prop} = array_slice($this->{$prop}, 0, $index) + array_slice($this->{$prop}, $index);

        array_splice($this->{$prop}, $index, 1);
        $this->subTotal($prop);
    }

    public function saveItem($index, $prop)
    {
        $this->{$prop}[$index]['saved'] = true;
        $this->subTotal($prop);
    }

    public function editItem($index, $prop)
    {
        $this->{$prop}[$index]['saved'] = false;
    }

    public function saveBill()
    {
        DB::beginTransaction();
        try {
            $bill = $this->visit->bills()->create([
                'status' => Status::pending->value,
                'created_by' => auth()->user()->id,
                'bill_number' => date('ym-') . str_pad(
                    Bill::whereRaw("EXTRACT(MONTH FROM created_at) = ? AND EXTRACT(YEAR FROM created_at) = ?", [date('m'), date('Y')])->count() + 1,
                    6,
                    "0",
                    STR_PAD_LEFT
                ) . "-{$this->visit->id}",
                'patient_id' => $this->visit->patient_id,
                'bill_date' => now(),
            ]);

            $this->saveTests($bill);
            $this->saveDrugs($bill);
            $this->saveScans($bill);

            foreach ($this->others as $o) {
                $bill->entries()->create([
                    'unit_price' => 0,
                    'total_price' => $o['product']['amount'],
                    'description' => $o['product']['name'],
                    'user_id' => $bill->created_by,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            notifyUserError($e->getMessage(), auth()->user());
            logger()->emergency("Unable to create bill: ");
            report($e);
            return;
        }

        notifyUserSuccess("Bill created successfully [#{$bill->id}]", auth()->user());
        !empty($this->tests) && notifyDepartment(
            Department::DIS->value,
            "Pending quotes for {$this->visit->patient->name}",
            ['title' => 'Pending Bill Quote']
        );
        $this->redirect(route('billing.patient-bills', [$this->visit->patient]));
    }

    public function saveTests(Bill $bill)
    {
        foreach ($this->tests as $test) {
            $bill->entries()->create([
                'chargeable_type' => Product::class,
                'user_id' => $bill->created_by,
                'chargeable_id' => $test['product']['id'],
                'unit_price' => $test['product']['amount'],
                'total_price' => $test['product']['amount'],
                'description' => $test['product']['name'],
                'tag' => 'test',
                'meta' => [
                    'id' => isset($test['data']) ? $test['data']['id'] : null,
                    'data' => $test['data'],
                ],
            ]);
        }
    }

    public function saveDrugs(Bill $bill)
    {
        // dd($this->drugs);
        foreach ($this->drugs as $d) {
            $bill->entries()->create([
                'chargeable_type' => PrescriptionLine::class,
                'chargeable_id' => $d['data']['id'],
                'user_id' => $bill->created_by,
                'unit_price' => !empty($d['product']) ? TreatmentService::getPrice($d['product']['id'], @$d['data']['profile'] ?? 'RETAIL') : 0,
                'total_price' => $d['total_amt'],
                'description' => !empty($d['product']) ? "{$d['product']['name']} {$d['data']['dosage']} for {$d['data']['dosage']} day(s)" : $d['data']['description'],
                'tag' => 'drug',
                'quantity' => floatval($d['data']['qty_dispensed'] ?? TreatmentService::getCount($d['product'], (object) $d['data']) ?? 0),
                'status' => $d['data']['status'], // !empty($d['product']) ? Status::active->value : Status::blocked->value,
            ]);
        }
    }

    public function saveScans(Bill $bill)
    {
        foreach ($this->scans as $d) {
            $bill->entries()->create([
                'chargeable_type' => Product::class,
                'user_id' => $bill->created_by,
                'chargeable_id' => $d['product']['id'],
                'unit_price' => $d['product']['amount'],
                'total_price' => $d['product']['amount'],
                'description' => $d['product']['name'],
                'tag' => 'scan',
                'meta' => [
                    'id' => isset($d['data']) ? $d['data']['id'] : null,
                    'data' => $d['data'],
                ]
            ]);
        }
    }

    public function addDrug($data)
    {
        $comp = ['product' => $data['product'], 'data' => $data['data'], 'saved' => true];

        $totalAmt = TreatmentService::getCount($data['product'], (object) $data['data']) * $data['product']['prices'][0]['price'];
        $comp['total_amt'] = $totalAmt;
        $this->drugs[] = ($comp);
    }
}
