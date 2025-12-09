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
use App\Models\StockItem;
use App\Services\TreatmentService;

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
    public $count = 0;

    public function mount($visit, $dispatch = false, $display = true)
    {
        $this->visit = $visit->load(['prescriptions.prescriptionable']);
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
        $this->visit->prescriptions()->where('id', $id)->delete();
        $this->visit->refresh();
        $this->dispatch('treatments_updated');
    }

    public function render()
    {
        return view('livewire.doctor.add-presciption');
    }

    // public function hydrate() {
    //     $this->getCount();
    // }

    public function getCount() {
        // try {
        //     //code...
        //     $freq = $this->requestForm->frequency;
        //     $dosage = $this->translateDosage($this->requestForm->dosage);
        //     $days = $this->requestForm->duration;

        //     $delta = round($dosage / $this->selections?->weight, 5);

        //     $freq = $this->analyzeFreqeuency($freq);

        //     // dump("Delta: " . $delta);
        //     $count = $delta * (max(intval($days), 1)) * max(intval($freq), 1);

        //     $this->count = $count;
        // } catch (\Throwable $th) {
        //     dump($th->getMessage());
        //     $this->count = 0;
        // }

        $this->count = TreatmentService::getCount((array) $this->selections, (object) $this->requestForm->all());
    }

    private function translateDosage($dosage) {
        $si_unit = strtolower($this->selections->si_unit);
        try {
            $matches = null;
            preg_match("/^((\d+)(\.(\d+))?)([a-zA-Z]+)?/", $dosage, $matches);

            $count = floatval($matches[1]);
            $si = isset($matches[5]) ? strtolower($matches[5]) : null;

            if ($si == null || $si == $si_unit) {
                return $count;
            }

            if ($si_unit == "m{$si}") {
                return $count * 1000;
            }
            if ($si == "m{$si_unit}") {
                return $count / 1000;
            }

            if ($si_unit == "k{$si}") {
                return $count / 1000;
            }
            if ($si == "k{$si_unit}") {
                return $count * 1000;
            }

            return 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    private function analyzeFreqeuency($freq) {
        return match ($freq) {
            'stat' => 1,
            'immediately' => 1,
            'od' => 1,
            'bd' => 2,
            'tds' => 3,
            'qds' => 4,
        };
    }
}
