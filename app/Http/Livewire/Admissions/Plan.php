<?php

namespace App\Http\Livewire\Admissions;

use App\Enums\Status;
use App\Models\Product;
use App\Services\Comms;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Plan extends Component
{
    public $visit;
    public $plans = [];

    /**
     * @var \Illuminate\Support\Collection
     */
    public $tests;

    /**
     * @var \Illuminate\Support\Collection
     */
    public $investigations;

    public $operationNote;
    public $admissionNote;
    public $surgery = '';

    #[Validate('required|string')]
    public $indication = '';

    public $admission;

    public function render()
    {
        return view('livewire.admissions.plan');
    }

    public function mount($visit, $admission = null)
    {
        if (!empty($admission)) {
            $this->tests = $admission->plan->tests;
            $this->investigations = $admission->plan->scans;

            $this->plans = $admission->plan->treatments;

            $this->admission = $admission;
            $this->indication = $this->admission->plan->indication;
        } else {
            $this->tests = collect([]);
            $this->investigations = collect([]);
        }
    }


    public function addPrescription($data)
    {
        if (empty($this->admission)) {
            $this->plans[] = $data;
            return;
        }

        $product = null;
        if (isset($data['product']['id'])) {
            $product = Product::find($data['product']['id']);
        }
        if (empty($product)) {
            $product = (object) ($data['product']);
        }

        logger()->info("Adding product: " . var_export($product, true));

        $this->admission->plan->addPrescription($this->admission->patient, $product, (object) $data, $this->admission->plan);

        $this->admission->refresh();
        $this->plans = $this->admission->plan->treatments;
    }

    public function removePlanItem($id, $index = null)
    {
        if (empty($this->admission)) {
            unset($this->plans[$id]);
            $this->plans = array_values($this->plans);
            return;
        }

        $this->admission->plan->treatments()->where('id', $index)->delete();

        $this->admission->refresh();
        $this->plans = $this->admission->plan->treatments;
    }

    public function savePlan()
    {
        $this->validate();

        $this->admission->plan->update([
            'indication' => $this->indication,
        ]);

        Comms::notifyUserSuccess("Admission updated successfully", auth()->user()->id);
    }

    public function addTest($data)
    {
        if (empty($this->admission)) {
            if ($this->tests->doesntContain("id", '=', $data['id'])) {
                $this->tests->add($data);
            }

            return;
        } else {
            $this->admission->plan->tests()->firstOrCreate([
                'describable_type' => Product::class,
                'describable_id' => $data['id'],
                'patient_id' => $this->admission->patient_id,
                'name' => $data['name'],
                'status' => Status::pending->value,
            ], [
                'describable_type' => Product::class,
                'describable_id' => $data['id'],
                'patient_id' => $this->admission->patient_id,
                'name' => $data['name'],
            ]);

            $this->admission->refresh();
            $this->tests = $this->admission->plan->tests;
        }
    }

    public function addInvestigation($data)
    {
        if (empty($this->admission)) {
            if ($this->investigations->doesntContain("id", "=", $data['id'])) {
                $this->investigations->add($data);
            }
            return;
        }

        $this->admission->plan->scans()->firstOrCreate([
            'describable_type' => Product::class,
            'describable_id' => $data['id'],
            'patient_id' => $this->admission->patient_id,
            'name' => $data['name'],
            'status' => Status::pending->value,
        ], [
            'describable_type' => Product::class,
            'describable_id' => $data['id'],
            'patient_id' => $this->admission->patient_id,
            'name' => $data['name'],
            'requested_by' => auth()->user()->id,
        ]);

        $this->admission->refresh();
        $this->investigations = $this->admission->plan->scans;
    }

    public function removeTest($id)
    {
        $this->tests = $this->tests->filter(fn($i) => $i['id'] != $id);
    }

    public function removeInvestigation($id)
    {
        $this->investigations = $this->investigations->filter(fn($i) => $i['id'] != $id);
    }
}
