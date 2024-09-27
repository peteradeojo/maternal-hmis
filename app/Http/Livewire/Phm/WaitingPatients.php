<?php

namespace App\Http\Livewire\Phm;

use App\Enums\Department;
use App\Enums\Status;
use App\Models\Documentation;
use App\Models\DocumentationPrescription;
use App\Models\Visit;
use Livewire\Component;

class WaitingPatients extends Component
{
    public $data = [];
    public function mount()
    {
        $query = Visit::with(['patient']);
        $this->data = match (auth()->user()->department_id) {
            Department::DIS->value => $query->whereHas('visit', function ($query) {
                $query->whereHas('treatments', fn ($q) => $q->whereIn('status', [Status::pending->value]));
            })->get(),
            Department::PHA->value => $query->whereHas('visit', function ($query) {
                $query->whereHas('treatments', fn ($q) => $q->whereIn('status', [Status::pending->value, Status::quoted->value]));
            })->get(),
            default => [],
        };
    }

    public function render()
    {
        return view('livewire.phm.waiting-patients');
    }
}
