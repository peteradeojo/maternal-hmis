<?php

namespace App\Http\Livewire\Doctor;

use App\Models\Visit;
use App\Models\Product;
use Livewire\Component;
use App\Dto\PrescriptionDto;
use App\Livewire\Forms\Doctor\PrescriptionRequest;
use App\Services\TreatmentService;

class AddPrescription extends Component
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
    public $count;

    public $canDelete = true;

    public function mount($visit, $dispatch = false, $display = true)
    {
        $this->visit = $visit->load(['prescriptions.prescriptionable', 'prescription']);
        $this->dispatchEvent = $dispatch;
        $this->display = $display;

        $this->selections = [];
    }

    public function addTempPrescription($detail)
    {
        $this->selections = Product::where('name', $detail['product'])->first() ?? (object) $detail['product'];
    }

    public function addPrescription($item)
    {
        $item['weight'] ??= null;
        $item['si_unit'] ??= null;
        $item['id'] ??= null;
        $this->selections = (object) $item;
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

        $this->dispatch('treatments_updated');
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
        $this->visit->prescription?->lines()->where('id', $id)->delete();
        $this->visit->refresh();
        $this->dispatch('treatments_updated');
    }

    public function render()
    {
        return view('livewire.doctor.add-prescription');
    }

    public function getCount()
    {
        if (isset($this->selections->id)) {
            $this->count = TreatmentService::getCount((array) $this->selections, (object) $this->requestForm->all());
        } else {
            $this->count = "No inventory";
        }
    }
}
