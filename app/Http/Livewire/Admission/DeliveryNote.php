<?php

namespace App\Http\Livewire\Admission;

use App\Enums\NoteCodes;
use App\Http\Livewire\Doctor\Consultation;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class DeliveryNote extends Component
{
    /**
     * @var \App\Models\Admission
     */
    public $admission;

    #[Validate('required|string')]
    public $note;

    public $prescriptions = [];

    public function render()
    {
        return view('livewire.admission.delivery-note');
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $this->admission->delivery_note()->create([
                'code' => NoteCodes::Delivery,
                'note' => $this->note,
                'consultant_id' => auth()->user()->id,
                'patient_id' => $this->admission->patient_id,
            ]);

            foreach ($this->prescriptions as $psc) {
                $product = isset($psc['productId']) ? Product::find($psc['productId']) : (object) $psc['product'];

                $this->admission->plan->addPrescription(
                    $this->admission->patient,
                    $product,
                    (object) $psc
                );
            }

            DB::commit();

            $this->dispatch('$refresh');
            notifyUserSuccess(
                "Delivery note has been saved.",
                auth()->user()->id,
                [
                    'timeout' => 10000,
                ]
            );
        } catch (\Throwable $th) {
            report($th);
            DB::rollBack();
            notifyUserError($th->getMessage(), auth()->user()->id, [
                'timeout' => 10000,
            ]);
        }
    }

    public function addPrescription($evt)
    {
        ['product' => $product] = $evt;
        array_push($this->prescriptions, $product);
    }
}
