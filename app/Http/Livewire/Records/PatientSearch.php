<?php

namespace App\Http\Livewire\Records;

use App\Models\Patient;
use App\Models\PatientCategory;
use Livewire\Component;

class PatientSearch extends Component
{
    public $categories;

    public $category;
    public $searchName;
    public $searchNumber;

    public $searchResults;

    public function mount()
    {
        $this->categories = PatientCategory::all();
    }

    public function render()
    {
        return view('livewire.records.patient-search');
    }

    public function search()
    {
        if (empty($this->searchNumber) && empty ($this->searchName)) {
            $this->searchResults = [];
            return;
        }

        $query = Patient::with(['category']);

        if (!empty($this->searchName)) {
            $query->where('name', 'like', "%{$this->searchName}%");
        }

        if (!empty($this->searchNumber)) {
            $query->where('name', 'like', "{$this->searchNumber}%");
        }

        if (!empty($this->category)) {
            $query->whereHas('category', function ($q) {
                $q->where('name', $this->category);
            });
        }

        $this->searchResults = $query->get();
    }
}
