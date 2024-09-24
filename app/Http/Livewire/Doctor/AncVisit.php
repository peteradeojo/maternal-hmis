<?php

namespace App\Http\Livewire\Doctor;

use App\Livewire\Forms\Doctor\AncFollowup;
use App\Models\Product;
use Exception;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AncVisit extends Component
{
    public AncFollowup $form;

    public $visit;
    public $anc_visit;

    public $return_visit;

    public $note;

    public function mount($visit)
    {
        $this->visit = $visit;
        $this->anc_visit = $visit->visit;

        $this->return_visit = Carbon::now()->addWeeks(3)->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.doctor.anc-visit');
    }

    public function removeTest($id)
    {
        $this->anc_visit->tests()->where('id', $id)->delete();
        $this->anc_visit->refresh();
    }

    public function addTest($id)
    {
        $pdt = Product::find($id);
        if ($pdt) {
            $this->anc_visit->tests()->create([
                'patient_id' => $this->visit->patient_id,
                'describable_type' => $pdt::class,
                'describable_id'  => $pdt->id,
                'name' => $pdt->name,
            ]);
        }

        $this->anc_visit->refresh();
    }

    public function addScan($id)
    {
        $pdt = Product::find($id);
        if (!$pdt) return;

        $this->anc_visit->radios()->create([
            'patient_id' => $this->visit->patient_id,
            'describable_type' => $pdt::class,
            'describable_id'  => $pdt->id,
            'name' => $pdt->name,
            'requested_by' => auth()->user()->id,
        ]);

        $this->anc_visit->refresh();
    }

    function  addPrescription($id) {
        $pdt = Product::find($id);
        if (!$pdt) return;

        $this->anc_visit->treatments()->create([
            'patient_id' => $this->visit->patient_id,
            'prescriptionable_type' => $pdt::class,
            'prescriptionable_id'  => $pdt->id,
            'name' => $pdt->name,
            'requested_by' => auth()->user()->id,
            'event_type' => $this->anc_visit::class,
            'event_id' => $this->anc_visit->id,
        ]);
    }

    public function addNote()
    {
        $this->anc_visit->notes()->create([
            'patient_id' => $this->visit->id,
            'note' => $this->note,
            'consultant_id' => auth()->user()->id,
        ]);
        $this->note = "";
        $this->anc_visit->refresh();
    }
}
