<?php

namespace App\Http\Livewire\Nurses;

use App\Enums\Status;
use App\Interfaces\OperationalEvent;
use App\Livewire\Forms\VitalsForm;
use App\Models\Visit;
use App\Models\Vitals as ModelsVitals;
use Livewire\Component;

class Vitals extends Component
{
    public $evt;

    public VitalsForm $vitals;

    public function render()
    {
        return view('livewire.nurses.vitals');
    }

    public function mount(OperationalEvent $event)
    {
        $this->evt = $event;
    }

    public function save()
    {
        $data = $this->vitals->validate();

        $data = array_filter($data);

        if (count($data) < 1) return;

        ModelsVitals::create([
            'patient_id' => $this->evt->patient_id,
            'recordable_type' => $this->evt::class,
            'recordable_id' => $this->evt->id,
            'recording_user_id' => auth()->user()->id,
            ...$data,
        ]);

        $this->vitals->reset();
        $this->vitals->resetErrorBag();

        $this->hydrate();
    }

    public function hydrate() {
        $this->evt->refresh();
    }
}
