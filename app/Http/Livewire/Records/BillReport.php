<?php

namespace App\Http\Livewire\Records;

use App\Models\Bill;
use App\Enums\Status;
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

    public function mount($visit)
    {
        $this->visit = $visit;

        $tests = $this->visit->tests->load('describable');

        if ($visit->type == "Antenatal") {
            $tests = $tests->merge($visit->visit->tests->load('describable'));
        }

        $this->tests = $tests->map(fn($test) => [
            'saved' => true,
            'product' => $test->describable->toArray(),
            'data' => $test->toArray()
        ]);

        $this->drugs = $this->visit->treatments->load('prescriptionable')->map(fn($item) => [
            'saved' => true,
            'product' => $item->prescriptionable->toArray(),
            'data' => $item->toArray(),
        ]);

        $this->scans = $this->visit->imagings->load('describable')->map(fn($item) => [
            'saved' => true,
            'product' => $item->describable->toArray(),
            'data' => $item->toArray(),
        ]);

        $this->others = [];
    }

    public function render()
    {
        return view('livewire.records.bill-report');
    }

    public function addItem($id, $prop)
    {
        $pdt = Product::find($id);

        // switch ($prop) {
        //     case 'tests':
        //         return $this->addTest($pdt);
        //     case 'drugs':
        //         return $this->addDrug($pdt);
        //     case 'scans':
        //         return $this->addScan($pdt);
        // }

        $this->{$prop}[] = ['saved' => true, 'data' => null, 'product' => $pdt->toArray()];
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

    public function subTotalOthers()
    {
        $this->others_amt = array_reduce($this->others, fn($a, $p) => $a + $p['amount'], 0);
    }

    public function subTotal($prop)
    {
        // dd($this->{$prop});
        $this->{$prop . "_amt"} = $this->{$prop}->reduce(fn($a, $p) => $a + $p['product']['amount'], 0);
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
            notifyUserError($e->getMessage());
            logger()->emergency("Unable to create bill: ");
            report($e);
            return;
        }

        notifyUserSuccess("Bill created successfully [#{$bill->id}]");
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
        $this->drugs->push($comp);
    }
}
