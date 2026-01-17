<?php

namespace App\Http\Livewire\Doctor;

use App\Enums\Status;
use App\Interfaces\OperationalEvent;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TreatmentPlans extends Component
{
    public OperationalEvent $origin;

    #[Validate('required|string')]
    public string $plan;

    public function mount($event)
    {
        $this->origin = $event->load(['treatment_plans']);
    }

    public function render()
    {
        return view('livewire.doctor.treatment-plans');
    }

    public function savePlan()
    {
        $p = $this->origin->treatment_plans()->create([
            'patient_id' => $this->origin->patient->id,
            'user_id' => request()->user()->id,
            'plan' => $this->plan,
            'status' => Status::active,
        ]);

        $this->dispatch('$refresh');
    }

    public function togglePlan($id)
    {
        $plan = $this->origin->treatment_plans()->where('id', $id)->first();
        $plan->update([
            'status' => $plan->status == Status::active ? Status::cancelled : Status::active,
        ]);
    }
}
