<?php

namespace App\Http\Livewire\Billing;

use App\Enums\Status;
use Livewire\Component;
use App\Models\BillPayment;
use Illuminate\Support\Str;

class MakePayment extends Component
{
    public $bill;
    public $amount;
    public $method = 'cash';

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

    public function methodAdjusted() {
        if ($this->method == "waived") {
            $this->amount = $this->bill->balance;
        }
    }
}
