<?php

namespace App\Http\Livewire\Doctor\Antenatal;

use App\Livewire\Forms\Antenatal\PhysicalExamForm;
use Livewire\Component;

class PhysicalExam extends Component
{
    public $profile;

    public PhysicalExamForm $physical;

    public function mount($profile) {
        $this->profile = $profile;
        $this->physical->fill($this->profile->examination ?? []);
    }

    public function render()
    {
        return view('livewire.doctor.antenatal.physical-exam');
    }

    public function save()
    {
        $this->validate();

        $this->profile->examination = $this->physical->all();
        $this->profile->save();

        $this->dispatch('$refresh');
    }
}
