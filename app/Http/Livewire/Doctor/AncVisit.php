<?php

namespace App\Http\Livewire\Doctor;

use App\Livewire\Forms\Doctor\AncFollowup;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AncVisit extends Component
{
    public AncFollowup $form;

    public $visit;

    public $return_visit;
    public $cancellable = true;

    public function mount($visit)
    {
        $this->visit = $visit->load(['complaints']);
        $this->return_visit = Carbon::now()->addWeeks(3)->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.doctor.anc-visit');
    }
}
