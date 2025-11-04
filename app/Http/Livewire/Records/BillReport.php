<?php

namespace App\Http\Livewire\Records;

use App\Models\Bill;
use App\Enums\Status;
use App\Models\Product;
use Livewire\Component;
use App\Models\BillDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        $this->tests = $this->visit->tests->load('describable')->pluck('describable')->toArray();

        if($visit->type == "Antenatal") {
            $this->tests = array_merge($this->tests, $visit->visit->tests->load('describable')->pluck('describable')->toArray());
        }

        $this->drugs = $this->visit->treatments->toArray();
        $this->scans = $this->visit->imagings->load('describable')->pluck('describable')->toArray();
        $this->others = [];

        // dd($this->tests);
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

        $this->{$prop}[] = [...$pdt->only(['name', 'id', 'amount']), 'saved' => true,];
        $this->subTotal($prop);
    }

    public function addTest($p)
    {
        $this->tests = [];
    }

    public function addDrug($p) {}

    public function addScan($p) {}

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
        $this->{$prop . "_amt"} = array_reduce($this->{$prop}, fn($a, $p) => $a + $p['amount'], 0);;
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
        // dd($this->others, $this->tests, $this->drugs, $this->scans);
        // $bill = $this->visit->bill;

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
                'total_price' => $o['amount'],
                'description' => $o['name'],
                'user_id' => $bill->created_by,
            ]);
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
                'chargeable_id' => $test['id'],
                'unit_price' => $test['amount'],
                'total_price' => $test['amount'],
                'description' => $test['name'],
                'tag' => 'test',
            ]);
        }
    }

    public function saveDrugs(Bill $bill)
    {
        foreach ($this->drugs as $d) {
            $bill->entries()->create([
                'chargeable_type' => Product::class,
                'user_id' => $bill->created_by,
                'chargeable_id' => $d['id'],
                'unit_price' => $d['amount'],
                'total_price' => $d['amount'],
                'description' => $d['name'],
                'tag' => 'drug',
            ]);
        }
    }

    public function saveScans(Bill $bill)
    {
        foreach ($this->scans as $d) {
            $bill->entries()->create([
                'chargeable_type' => Product::class,
                'user_id' => $bill->created_by,
                'chargeable_id' => $d['id'],
                'unit_price' => $d['amount'],
                'total_price' => $d['amount'],
                'description' => $d['name'],
                'tag' => 'scan',
            ]);
        }
    }
}
