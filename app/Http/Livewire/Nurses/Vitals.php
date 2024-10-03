<?php

namespace App\Http\Livewire\Nurses;

use App\Enums\Status;
use App\Models\Visit;
use Livewire\Component;

class Vitals extends Component
{
    public $vitals;

    public function render()
    {
        return view('livewire.nurses.vitals');
    }

    public function load()
    {
        $this->vitals = Visit::with(['patient'])->whereHas('visit', function ($query) {
            $query->doesntHave('vitals');
        })->where('status', '!=', Status::completed->value)->latest()->limit(30)->get();
    }

    public function mount() {
        $this->load();
    }

    public function hydrate() {
        $this->dispatch("update-table");
    }
}
