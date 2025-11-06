<?php

namespace App\Http\Livewire\Billing;

use App\Enums\Status;
use Livewire\Component;

class VisitBills extends Component
{
    public $visit;
    public $status = Status::pending->value;

    public function render()
    {
        return view('livewire.billing.visit-bills');
    }
}
