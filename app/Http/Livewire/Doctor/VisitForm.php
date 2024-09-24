<?php

namespace App\Http\Livewire\Doctor;

use App\Models\Product;
use App\Models\Visit;
use Livewire\Component;

class VisitForm extends Component
{
    /**
     * @var Visit
     */
    public $visit;
    public $profile;

    public $tests = [];
    public $diagnoses = [];

    public $editingLmp = false;
    public $lmpEdit;

    public  function editLmp()
    {
        $this->editingLmp = true;
        $this->lmpEdit = $this->profile->lmp;
    }

    public function updateLmp()
    {
        $this->profile->lmp = $this->lmpEdit;
        $this->profile->save();
        $this->editingLmp = false;
    }

    public function render()
    {
        return view('livewire.doctor.visit-form');
    }

    public function mount($visit)
    {
        $this->visit = $visit->load(['visit']);
        if ($visit->readable_visit_type == 'Antenatal') {
            $this->profile = $visit->patient->antenatalProfiles[0];
        }
    }

    public function addTest($id)
    {
        $pdt = Product::find($id);
        $this->visit->tests()->create([
            'patient_id' => $this->visit->patient_id,
            'describable_type' => $pdt::class,
            'describable_id' => $pdt->id,
            'name' => $pdt->name,
        ]);
    }

    public function addScan($id)
    {
        $pdt = Product::find($id);
        $this->visit->imagings()->create([
            'patient_id' => $this->visit->patient_id,
            'describable_type' => $pdt::class,
            'describable_id' => $pdt->id,
            'requested_by' => auth()->user()->id,
            'name' => $pdt->name,
        ]);
    }
}
