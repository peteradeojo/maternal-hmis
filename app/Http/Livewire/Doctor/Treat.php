<?php

namespace App\Http\Livewire\Doctor;

use App\Models\ConsultationNote;
use App\Models\Product;
use Illuminate\Support\Facades\Broadcast;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Treat extends Component
{
    public $visit;
    public $note;
    public $complaint;
    public $complaint_duration;

    #[Validate('required|string')]
    public $diagnoses;

    public function render()
    {
        return view('livewire.doctor.treat');
    }

    public function addScan($id)
    {
        $pdt = Product::find($id);
        if (!$pdt) return;

        $this->visit->radios()->create([
            'patient_id' => $this->visit->patient_id,
            'describable_type' => $pdt::class,
            'describable_id'  => $pdt->id,
            'name' => $pdt->name,
            'requested_by' => auth()->user()->id,
        ]);

        $this->visit->refresh();
    }

    public function addTest($id)
    {
        $pdt = Product::find($id);
        if ($pdt) {
            $this->visit->tests()->create([
                'patient_id' => $this->visit->patient_id,
                'describable_type' => $pdt::class,
                'describable_id'  => $pdt->id,
                'name' => $pdt->name,
            ]);
        }

        $this->visit->refresh();
    }

    public function removeTest($id)
    {
        $this->visit->tests()->where('id', $id)->delete();
        $this->visit->refresh();
    }

    public function addPrescription($id)
    {
        $pdt = Product::find($id);
        if (!$pdt) return;

        $this->visit->treatments()->create([
            'patient_id' => $this->visit->patient_id,
            'prescriptionable_type' => $pdt::class,
            'prescriptionable_id'  => $pdt->id,
            'name' => $pdt->name,
            'requested_by' => auth()->user()->id,
        ]);
    }

    public function addNote()
    {
        $this->visit->notes()->create([
            'patient_id' => $this->visit->patient->id,
            'note' => $this->note,
            'consultant_id' => auth()->user()->id,
        ]);
        $this->note = "";
        $this->visit->refresh();

        $this->dispatch('close-anc-visit-notes-modal');
    }

    public function removeScan($id)
    {
        $this->visit->radios()->where('id', $id)->delete();
        $this->visit->refresh();
    }

    public function takeComplaint()
    {
        $this->validate([
            'complaint' => 'required|string',
            'complaint_duration' => 'nullable|string'
        ]);

        $this->visit->histories()->create([
            'presentation' => $this->complaint,
            'duration' => $this->complaint_duration,
            'patient_id' => $this->visit->patient->id,
        ]);

        $this->dispatch('$refresh');
        $this->reset('complaint', 'complaint_duration');
    }

    public function removeNote($id)
    {
        $note = ConsultationNote::find($id);
        if (request()->user()->can('delete', $note)) {
            $this->visit->notes()->where('id', $id)->delete();
            $this->dispatch('$refresh');
        }
    }

    public function removeComplaint($id)
    {
        $this->visit->histories()->where('id', $id)->delete();
        $this->dispatch('$refresh');
    }

    public function addTreatment($data)
    {
        if (!empty($data['id'])) {
            $product = Product::find($data['id']);
        } else {
            $product = (object) $data['product'];
        }
        $this->visit->addPrescription($this->visit->patient, $product);
        $this->dispatch('$refresh');
    }

    public function addedTreatment() {
        $this->dispatch('$refresh');
        // $this->dispatch('close-anc-treatments');
    }

    public function addDiagnosis() {
        $data = $this->validate();

        $this->visit->diagnoses()->create([
            ...$data,
            'patient_id' => $this->visit->patient_id,
            'user_id' => auth()->user()->id,
        ]);

        $this->reset('diagnoses');
        $this->dispatch('$refresh');

        Broadcast::on("doc-update.{$this->visit->id}")->as('DocUpdate')->sendNow();
    }

    public function removeDiagnosis($id) {
        $this->visit->diagnoses()->where('id', $id)->delete();
        $this->dispatch('$refresh');
        Broadcast::on("doc-update.{$this->visit->id}")->as('DocUpdate')->sendNow();
    }
}
