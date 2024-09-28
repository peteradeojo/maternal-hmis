<?php

namespace App\Http\Livewire\Doctor;

use Livewire\Component;
use App\Livewire\Forms\Doctor\AncBookingForm;
use App\Models\Product;

class AncBooking extends Component
{
    /**
     * @var \App\Models\AntenatalProfile
     */
    public $profile;

    /**
     * @var \App\Models\Visit
     */
    public $visit;

    public AncBookingForm $bookingForm;

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
        dump($id);
    }
}
