<?php

namespace App\Http\Livewire\Doctor;

use App\Livewire\Forms\Doctor\AncFollowup;
use App\Models\Product;
use App\Models\Visit;
use Exception;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AncVisit extends Component
{
    public AncFollowup $form;

    public $visit;

    public $return_visit;

    public $note;
    public $cancellable = true;

    public $loadedVisit = null;

    public function mount($visit)
    {
        $this->visit = $visit;

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

    function  addPrescription($id)
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
    }

    public function removeScan($id)
    {
        $this->visit->radios()->where('id', $id)->delete();
        $this->visit->refresh();
    }

    public function loadVisitReport($id)
    {
        $this->loadedVisit = Visit::find($id)?->visit->load(['notes', 'tests', 'prescriptions', 'radios']);
    }
}
