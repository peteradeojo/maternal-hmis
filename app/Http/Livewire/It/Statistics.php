<?php

namespace App\Http\Livewire\It;

use App\Enums\Status;
use App\Models\Visit;
use Livewire\Component;

class Statistics extends Component
{
    public $data = [];

    public function mount() {
        $completedVisits = Visit::whereIn('status', [Status::active->value])->orWhere(function ($query) {
            $query->whereNot('awaiting_doctor');
        })->count();

        $this->data = compact('completedVisits');
    }

    public function render()
    {
        return view('livewire.it.statistics');
    }
}
