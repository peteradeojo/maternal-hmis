<?php

namespace App\Http\Livewire\Records;

use App\Models\Product;
use Livewire\Component;

class BillReport extends Component
{
    public $visit;
    public $grandTotal = 0;

    public $others = [];
    public $otherAmt = 0;

    public function mount($visit)
    {
        $this->visit = $visit;
    }

    public function render()
    {
        return view('livewire.records.bill-report');
    }

    public function addItem($id)
    {
        $pdt = Product::find($id);
        $this->others[] = $pdt;
        $this->subTotal();
    }

    public function subTotal()
    {
        $this->otherAmt = array_reduce($this->others, fn ($a, $p) => $a + $p->amount, 0);
    }

    public function removeItem($id)
    {
        $this->others = array_filter($this->others, function ($p) use ($id) {
            return $p->id !== $id;
        });
        $this->subTotal();
    }
}
