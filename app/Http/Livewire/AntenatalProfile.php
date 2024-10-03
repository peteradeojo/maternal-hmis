<?php

namespace App\Http\Livewire;

use App\Models\AntenatalProfile as ModelsAntenatalProfile;
use Livewire\Component;

class AntenatalProfile extends Component
{
    public $profile;

    public $editingLmp = false;
    public $lmpEdit;
    public $editEdd;
    public $editingEdd = false;

    public $obsEdit = false;

    public function render()
    {
        return view('livewire.antenatal-profile');
    }

    public function mount(ModelsAntenatalProfile $profile)
    {
        $this->profile = $profile;

        $this->editEdd = $this->profile->edd;
        $this->lmpEdit = $this->profile->lmp;
    }

    public function updateEdd()
    {
        $this->profile->edd = $this->editEdd;
        $this->profile->lmp = null;

        $this->profile->calculateEddLmp(true);
        $this->editingEdd = false;
    }

    public function setEditingEdd()
    {
        $this->editingEdd = true;
        $this->editingLmp = false;
        $this->editEdd = $this->profile->edd;
    }

    public  function editLmp()
    {
        $this->editingLmp = true;
        $this->editingEdd = false;
        $this->lmpEdit = $this->profile->lmp;
    }

    public function updateLmp()
    {
        $this->profile->lmp = $this->lmpEdit;
        $this->profile->edd = null;
        $this->profile->save();

        $this->profile->calculateEddLmp(true);
        $this->editingLmp = false;
    }

    public function toggleEditObsData()
    {
        $this->obsEdit = !$this->obsEdit;
    }
}
