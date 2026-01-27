<?php

namespace App\Http\Livewire\Nursing;

use App\Enums\Department;
use App\Enums\Status;
use App\Models\AntenatalProfile;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Livewire\Component;

class AncBookings extends Component
{
    public $patientId = "";

    public User $user;

    public $resetChildComponent = 0;

    public AntenatalProfile|null $profile;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function load()
    {
        // unset($this->profile);
        // $this->profile = null;
        if (strlen($this->patientId) > 0) {
            $this->profile = AntenatalProfile::where('id', $this->patientId)->first();
            $this->resetChildComponent++;
            return;
        }
    }

    public function render()
    {
        if ($this->user->hasRole('doctor')) {
            return view('doctors.anc-booking-form');
        }
        if ($this->user->hasRole('lab')) {
            return view('lab.anc-bookings');
        }
        return view('livewire.nursing.anc-bookings');
    }
}
