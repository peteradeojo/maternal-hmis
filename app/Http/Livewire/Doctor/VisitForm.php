<?php

namespace App\Http\Livewire\Doctor;

use App\Livewire\Forms\Doctor\ExaminationForm;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Product;
use Livewire\Component;
use App\Models\AncVisit;
use App\Models\GeneralVisit;
use App\Models\PatientHistory;
use App\Livewire\Forms\Doctor\HistoryForm;

class VisitForm extends Component
{
    public $visit;
    public $profile;

    public $tests = [];
    public $diagnoses = [];

    public $histories;

    public HistoryForm $historyForm;
    public ExaminationForm $examForm;

    public function refreshProfile()
    {
        $this->profile->refresh();
    }

    public function mount($visit)
    {
        $this->visit = $visit;
        if ($visit->readable_visit_type == 'Antenatal') {
            $this->profile = $visit->patient->antenatalProfiles[0];
        }

        $this->histories = PatientHistory::selectRaw("presentation, count(presentation) as freq")->groupBy('presentation')->orderBy('freq', 'desc')->get();
    }

    public function addTest($id)
    {
        $pdt = Product::find($id);
        $this->visit->tests()->create([
            'patient_id' => $this->visit->patient_id,
            'describable_type' => $pdt::class,
            'describable_id' => $pdt->id,
            'name' => $pdt->name,
        ]);
        $this->visit->refresh();
    }

    public function addScan($id)
    {
        $pdt = Product::find($id);
        $this->visit->imagings()->create([
            'patient_id' => $this->visit->patient_id,
            'describable_type' => $pdt::class,
            'describable_id' => $pdt->id,
            'requested_by' => auth()->user()->id,
            'name' => $pdt->name,
        ]);

        $this->visit->refresh();
    }

    public function refresh()
    {
        $this->visit->refresh();
        $this->profile->calculateEddLmp(true);
        $this->hydrate();
    }

    public function addHistory()
    {
        $this->historyForm->validate();

        $this->visit->histories()->create([
            'patient_id' => $this->visit->patient_id,
            ...($this->historyForm->all()),
        ]);

        $this->historyForm->reset();

        $this->visit->refresh();
        $this->histories = PatientHistory::selectRaw("presentation, count(presentation) as freq")->groupBy('presentation')->orderBy('freq', 'desc')->get();
    }

    public function removeScan($id)
    {
        $this->visit->radios()->where('id', $id)->delete();
        $this->refresh();
    }

    public function render()
    {
        return view('livewire.doctor.visit-form');
    }

    public $loadedVisit = null;
    public function loadVisitReport($id)
    {
        $this->loadedVisit = Visit::find($id)?->visit->load(['notes', 'tests', 'prescriptions', 'radios']);
    }
}
