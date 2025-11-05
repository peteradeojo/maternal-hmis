<?php

namespace App\Http\Livewire\Doctor;

use App\Models\Visit;
use App\Models\Product;
use Livewire\Component;
use App\Models\AncVisit;
use App\Dto\PrescriptionDto;
use Illuminate\Support\Collection;
use App\Models\DocumentationPrescription;
use App\Livewire\Forms\Doctor\PrescriptionRequest;

class AddPresciption extends Component
{
    /**
     * @var Visit
     */
    public $visit;
    public $search;

    public $dispatchEvent;
    public $display;

    public PrescriptionRequest $requestForm;

    public $selections = null;

    public $updating = null;

    public $results = null;

    public $title;

    public function mount($visit, $dispatch = false, $display = true)
    {
        $this->visit = $visit->load(['prescriptions.prescriptionable']);
        $this->dispatchEvent = $dispatch;
        $this->display = $display;

        $this->selections = [];
    }

    public function addTempPrescription($detail) {
        $this->selections = Product::where('name', $detail['product'])->first() ?? (object) $detail['product'];
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
        $dto->productId = $this->selections->id ?? null;
        $dto->setProduct($this->selections);
        $dto->setDosage($this->requestForm->dosage);
        $dto->setDuration($this->requestForm->duration);
        $dto->setFrequency($this->requestForm->frequency);

        if ($this->dispatchEvent) {
            $this->dispatch("prescription_selected", product: $dto);
            $this->requestForm->reset();
            $this->selections = null;
            return;
        }

        $this->visit->addPrescription($this->visit->patient, $this->selections, $dto, $this->visit);
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
