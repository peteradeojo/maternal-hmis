<?php

namespace App\Http\Livewire\Doctor;

use App\Dto\PrescriptionDto;
use Livewire\Component;
use App\Livewire\Forms\Doctor\AncBookingForm;
use App\Models\Product;
use Livewire\Attributes\Validate;

class AncBooking extends Component
{
    /**
     * @var \App\Models\AntenatalProfile
     */
    public $profile;

    /**
     * @var \App\Models\AncVisit
     */
    public $visit;

    public AncBookingForm $bookingForm;

    public $treatments = [];
    public $selected_treatment = null;

    #[Validate('required|string')]
    public $treatment_dosage = null;

    #[Validate('required|string')]
    public $treatment_duration = null;

    #[Validate('required|string')]
    public $treatment_frequency = null;

    public function mount($profile, $visit)
    {
        $this->profile = $profile->load(['tests']);
        $this->visit = $visit;

        $this->bookingForm->fill($this->profile);
    }

    public function submitBooking()
    {
        $this->validate();

        $this->profile->update($this->bookingForm->all());
        $this->profile->refresh();

        $this->visit->update($this->bookingForm->except(['gravida', 'parity']));
        $this->visit->refresh();

        $this->bookingForm->reset();

        $this->dispatch("anc-profile-refresh")->to(VisitForm::class);
    }

    public function render()
    {
        return view('livewire.doctor.anc-booking');
    }

    public function addTest($data)
    {
        $this->profile->tests()->create([
            'name' => $data['name'],
            'describable_type' => Product::class,
            'describable_id' => $data['id'],
            'patient_id' =>  $this->visit->patient_id,
        ]);

        $this->profile->refresh();
    }

    public function removeTest($id)
    {
        $this->profile->tests()->where('id', $id)->delete();
    }

    public function addPrescription($id)
    {
        $pd = Product::find($id);
        if ($pd) {
            $this->selected_treatment = $pd;
        }
    }

    public function  cancelPrescription()
    {
        $this->selected_treatment = null;
        $this->reset('treatment_dosage', 'treatment_duration', 'treatment_frequency');
    }

    public function removePrescription($id)
    {
        $this->visit->treatments()->where('id', $id)->delete();
        $this->visit->refresh();
    }

    public function savePrescription()
    {
        if ($this->selected_treatment) {
            $this->validate();

            $dto = new PrescriptionDto();
            $dto->setProduct($this->selected_treatment);
            $dto->setDosage($this->treatment_dosage);
            $dto->setDuration($this->treatment_duration);
            $dto->setFrequency($this->treatment_frequency);

            $this->visit->addPrescription($this->visit->patient, $this->selected_treatment, $dto, $this->visit->visit);
            $this->visit->refresh();

            $this->selected_treatment = null;
        }
    }

    public function addScan($data)
    {
        $this->visit->imagings()->create([
            'name' => $data['name'],
            'describable_id' => $data['id'],
            'describable_type' => Product::class,
            'patient_id' => $this->visit->patient_id,
            'requested_by' => auth()->user()->id,
        ]);
    }

    public function removeScan($id)
    {
        $this->visit->imagings()->where('id', $id)->delete();
        $this->visit->refresh();
    }
}
