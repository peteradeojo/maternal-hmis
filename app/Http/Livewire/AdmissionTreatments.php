<?php

namespace App\Http\Livewire;

use App\Models\Admission;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdmissionTreatments extends Component
{
    public Admission $admission;

    private function getTreatments()
    {
        // $treatments = DB::table('admission_treatment_administrations')->where('admission_id');
        $treatments = $this->admission->administrations;
    }

    public function render()
    {
        return view('livewire.admission-treatments');
    }
}
