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

    public function mount($profile, $visit)
    {
        $this->profile = $profile->load(['tests']);
        $this->visit = $visit;
    }

    public function render()
    {
        return view('livewire.doctor.anc-booking');
    }

    public function initLabTests()
    {
        $this->profile->initLabTests();
        $this->profile->refresh();
        $this->dispatch('$refresh');
    }
}
