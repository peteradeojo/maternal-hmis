<?php

namespace App\Http\Livewire\Nhi;

use App\Interfaces\OperationalEvent;
use App\Models\InsuranceAuthorization;
use App\Models\Visit;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EncounterAuthorizations extends Component
{
    public Visit $visit;

    #[Validate('required|numeric')]
    public ?float $requested_amount = 0;

    #[Validate('nullable|numeric')]
    public ?float $approved_amount = 0;

    #[validate('nullable|string')]
    public ?string $authorization_code = null;

    public $editingAuthorization = null;

    public function mount(Visit $visit)
    {
        $this->visit = $visit->load('authorizations');
    }

    public function render()
    {
        return view('livewire.nhi.encounter-authorizations');
    }

    public function setEditing($id)
    {
        $auth = InsuranceAuthorization::find($id);

        if (! $auth) {
            return;
        }

        $this->authorization_code = $auth->authorization_code;
        $this->requested_amount = $auth->requested_amount;
        $this->approved_amount = $auth->approved_amount;
    }

    public function save()
    {
        $this->validate();

        InsuranceAuthorization::updateOrCreate([
            'authorization_code' => $this->authorization_code,
            'authorizable_type' => ($this->visit)::class,
            'authorizable_id' => $this->visit->id,
        ], [
            'patient_id' => $this->visit->patient_id,
            'profile_id' => $this->visit->patient->insure->id,
            'authorization_code' => $this->authorization_code,
            'requested_amount' => $this->requested_amount,
            'approved_amount' => $this->approved_amount,
        ]);
        $this->visit->refresh()->load('authorizations'); //->authorizations->refresh();

        $this->reset();
    }

    public function deleteAuthorization($id)
    {
        InsuranceAuthorization::where('id', $id)->delete();
    }
}
