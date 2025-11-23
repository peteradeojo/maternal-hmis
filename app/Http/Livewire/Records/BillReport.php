<?php

namespace App\Http\Livewire\Records;

use App\Enums\Department;
use App\Models\Bill;
use App\Enums\Status;
use App\Interfaces\OperationalEvent;
use App\Models\Product;
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

    public function loadBillData(?OperationalEvent $evt) {
        if (empty($evt)) return;

        $drugs = $evt->treatments;
        $tests = $evt->valid_tests;
        $scans = $evt->radios;

        $drugs = $drugs->map(fn($item) => [
            'saved' => true,
            'product' => $item->prescriptionable->toArray(),
            'data' => $item->toArray(),
        ])->toArray();

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
            'saved' => false,
        ];

        $this->subTotal('others');
    }

    public function subTotal($prop)
    {
        $this->{$prop . "_amt"} = collect($this->{$prop})->reduce(fn($a, $p) => $a + $p['product']['amount'], 0);
    }

    public function removeItem($index, $prop)
    {
        if ($index == 0) {
            array_shift($this->{$prop});
            return;
        }

        if ($index == count($this->{$prop}) - 1) {
            return array_pop($this->{$prop});
        }

        $this->{$prop} = array_slice($this->{$prop}, 0, $index) + array_slice($this->{$prop}, $index);
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
                    Bill::whereRaw("MONTH(created_at) = ? AND YEAR(created_at) = ?", [date('m'), date('Y')])->count() + 1,
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
        foreach ($this->drugs as $d) {
            $useNull = !isset($d['product']['id']);

            $bill->entries()->create([
                'chargeable_type' => $useNull ? null : Product::class,
                'user_id' => $bill->created_by,
                'chargeable_id' => $useNull ? null : $d['product']['id'],
                'unit_price' => $d['data']['amount'] ?? $d['product']['amount'] ?? 0,
                'total_price' => $d['data']['amount'] ?? $d['product']['amount'] ?? 0,
                'description' => $d['data']['name'],
                'tag' => 'drug',
                'meta' => [
                    'id' => $d['data']['id'] ?? null,
                    'data' => $d['data'],
                ],
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
        $this->drugs[] = ($comp);
    }
}
