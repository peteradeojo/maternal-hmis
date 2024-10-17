<?php

namespace App\Http\Livewire\Records;

use App\Models\Visit;
use Livewire\Component;

class VisitSearch extends Component
{
    public $name_query;
    public $number_query;
    public $visits;

    public function search()
    {
        if (empty($this->name_query) && empty($this->number_query)) {
            $this->visits = null;
            return;
        }

        $this->visits = Visit::with(['patient'])->whereHas('patient', function ($q) {
            if (!empty($this->name_query)) {
                $q->where('name', 'like', "{$this->name_query}%");
            }

            if (!empty($this->number_query)) {
                $q->Where('card_number', 'like', "{$this->number_query}");
            }
        })->latest()->limit(30)->get();
    }

    public function render()
    {
        return view('livewire.records.visit-search');
    }
}
