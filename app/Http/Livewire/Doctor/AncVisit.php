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

    public $note;
    public $cancellable = true;

    public $complaint;
    public $complaint_duration;

    public function mount($visit)
    {
        $this->visit = $visit->load(['complaints']);
        $this->return_visit = Carbon::now()->addWeeks(3)->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.doctor.anc-visit');
    }

    public function removeTest($id)
    {
        $this->visit->tests()->where('id', $id)->delete();
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
            'patient_id' => $this->visit->id,
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

        $this->visit->complaints()->create([
            'name' => $this->complaint,
            'duration' => $this->complaint_duration,
        ]);

        $this->dispatch('$refresh');
        $this->reset('complaint', 'complaint_duration');
    }

    public function removeNote($id)
    {
        $this->visit->notes()->where('id', $id)->delete();
        $this->dispatch('$refresh');
    }

    public function removeComplaint($id)
    {
        $this->visit->complaints()->where('id', $id)->delete();
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
}
