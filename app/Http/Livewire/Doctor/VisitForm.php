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
    /**
     * @var AncVisit|GeneralVisit
     */
    public $visit;
    public $profile;

    public $tests = [];
    public $diagnoses = [];

    public $editingLmp = false;
    public $lmpEdit;
    public $editEdd;

    public $histories;

    public HistoryForm $historyForm;
    public ExaminationForm $examForm;

    public function updateEdd()
    {
        $this->profile->edd = $this->editEdd;
        $this->redirect('');
    }

    public  function editLmp()
    {
        $this->editingLmp = true;
        $this->lmpEdit = $this->profile->lmp;
    }

    public function updateLmp()
    {
        $this->profile->lmp = $this->lmpEdit;
        $this->profile->save();
        $this->editingLmp = false;
    }

    public function render()
    {
        return view('livewire.doctor.visit-form');
    }

    public function mount($visit)
    {
        $this->visit = $visit;
        if ($visit->type == 'Antenatal') {
            $this->profile = $visit->patient->anc_profile;
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

    public function refresh() {
        $this->visit->refresh();
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
}
