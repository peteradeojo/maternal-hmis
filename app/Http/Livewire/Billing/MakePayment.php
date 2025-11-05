<?php

namespace App\Http\Livewire\Billing;

use App\Enums\Status;
use App\Models\BillDetail;
use Livewire\Component;
use App\Models\BillPayment;
use Illuminate\Support\Str;

class MakePayment extends Component
{
    public $bill;
    public $amount;
    public $method = 'cash';

    public $items = [];

    public $initHash = null;
    public $currentHash = null;

    public function mount($bill)
    {
        $this->bill = $bill;
        $this->resetHash();
    }

    public function render()
    {
        return view('livewire.billing.make-payment');
    }

    public function pay()
    {
        BillPayment::create([
            'amount' => $this->amount,
            'payment_method' => $this->method,
            'reference' => Str::random(16),
            'status' => Status::PAID->value,
            'bill_id' => $this->bill->id,
            'payment_date' => now(),
            'user_id' => auth()->user()->id,
        ]);

        $this->bill->refresh();

        if ($this->bill->balance <= 0) {
            $this->bill->status = Status::PAID->value;
            $this->bill->save();
        }

        $this->reset('amount', 'method');
        $this->dispatch('$refresh');
    }

    public function methodAdjusted()
    {
        if ($this->method == "waived") {
            $this->amount = $this->bill->balance;
        }
    }

    public function edit($i)
    {
        $this->items[$i]['saved'] = false;
    }

    public function save($i)
    {
        $this->items[$i]['saved'] = true;
        $this->items[$i]['tag'] == 'drug' ?
            ($this->items[$i]['amount'] = $this->items[$i]['total_price'] * 1.5) : ($this->items[$i]['amount'] = $this->items[$i]['total_price']);


        $this->updateHash();
        $this->dispatch('$refresh');
    }

    private function updateHash()
    {
        $this->currentHash = md5(json_encode($this->items));
    }

    public function updateBillDetailsAmt()
    {
        foreach ($this->items as $i => $item) {
            BillDetail::where('id', $item['id'])->update([
                'total_price' => $item['total_price'],
            ]);
        }

        $this->dispatch('$refresh');
        $this->resetHash();
    }

    private function resetHash()
    {
        $this->getItems();
        $this->initHash = $this->currentHash = md5(json_encode($this->items));
    }

    private function getItems()
    {
        $this->items = $this->bill->entries->map(fn($b) => [
            'description' => $b->description,
            'amount' => $b->amount,
            'total_price' => $b->total_price,
            'tag' => $b->tag,
            'saved' => true,
            'id' => $b->id,
        ])->toArray();
    }

    public function isDiff()
    {
        return $this->currentHash == $this->initHash;
    }
}
