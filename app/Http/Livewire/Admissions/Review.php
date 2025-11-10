<?php

namespace App\Http\Livewire\Admissions;

use App\Models\ConsultationNote;
use Livewire\Component;

class Review extends Component
{
    public $admission;

    public $note = '';

    public function render()
    {
        return view('livewire.admissions.review');
    }

    public function save() {
        $this->admission->notes()->create([
            'note' => $this->note,
            'consultant_id' => auth()->user()->id,
            'patient_id' => $this->admission->patient_id,
        ]);

        $this->dispatch('$refresh');
        $this->reset('note');
    }

    public function deleteNote($id) {
        $this->admission->notes()->where('id', $id)->delete();
        $this->dispatch('$refresh');
    }
}
