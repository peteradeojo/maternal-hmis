<?php

namespace App\Http\Livewire\Nhi;

use App\Enums\Status;
use App\Models\Documentation;
use Livewire\Component;

class PendingAuthorizations extends Component
{
    public $data;

    public function mount() {
        $this->data = Documentation::whereHas('patient', function ($query) {
            $query->has('insurance');
        })->where('status', Status::active->value)->get();
    }

    public function render()
    {
        return view('livewire.nhi.pending-authorizations');
    }
}
