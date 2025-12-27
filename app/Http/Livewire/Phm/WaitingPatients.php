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
        if (auth()->user()->hasRole('billing')) {
            $this->data = $query->whereHas('visit', function ($query) {
                $query->whereHas('treatments', fn($q) => $q->whereIn('status', [Status::pending->value]));
            })->get();
        } elseif (auth()->user()->hasRole('pharmacy')) {
            $this->data = $query->whereHas('visit', function ($query) {
                $query->whereHas('treatments', fn($q) => $q->whereIn('status', [Status::pending->value, Status::quoted->value]));
            })->get();
        } else {
            $this->data = [];
        }
    }

    public function render()
    {
        return view('livewire.phm.waiting-patients');
    }
}
