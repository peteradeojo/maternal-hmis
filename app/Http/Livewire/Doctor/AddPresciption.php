<?php

namespace App\Http\Livewire\Doctor;

use App\Dto\PrescriptionDto;
use App\Livewire\Forms\Doctor\PrescriptionRequest;
use App\Models\DocumentationPrescription;
use App\Models\Product;
use App\Models\Visit;
use Illuminate\Support\Collection;
use Livewire\Component;

class AddPresciption extends Component
{
    public Visit $visit;
    public $search;

    public PrescriptionRequest $requestForm;

    public $selections = null;

    public $updating = null;

    public $results = null;

    public function mount($visit)
    {
        $this->visit = $visit->load(['prescriptions.prescriptionable']);
    }

    public function addPrescription($id)
    {
        $product = Product::find($id);
        $this->selections = $product;
    }

    public function saveRequest()
    {
        $this->validate();

        $dto = new PrescriptionDto([]);
        $dto->productId = $this->selections->id;
        $dto->setProduct($this->selections);
        $dto->setDosage($this->requestForm->dosage);
        $dto->setDuration($this->requestForm->duration);
        $dto->setFrequency($this->requestForm->frequency);

        $this->visit->addPrescription($this->visit->patient, $this->selections, $dto);
        $this->visit->refresh();

        $this->selections = null;
    }

    public function edit($id)
    {
        $this->updating = $this->visit->prescriptions()->where('id', $id)->first();

        $this->requestForm->dosage = $this->updating->dosage;
        $this->requestForm->duration = $this->updating->duration;
        $this->requestForm->frequency = $this->updating->frequency;

        $this->selections = null;
    }

    public function cancel()
    {
        $this->selections = null;
    }

    public function saveUpdate()
    {
        $this->updating->update($this->requestForm->all());
        $this->visit->refresh();
        $this->updating = null;
    }

    public function cancelEdit()
    {
        $this->updating = null;
    }

    public function deleteRequestItem($id)
    {
        $this->visit->prescriptions()->where('id', $id)->delete();
        $this->visit->refresh();
    }

    public function render()
    {
        return view('livewire.doctor.add-presciption');
    }
}
