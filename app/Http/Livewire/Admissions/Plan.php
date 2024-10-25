<?php

namespace App\Http\Livewire\Admissions;

use App\Models\Admission;
use App\Models\AdmissionPlan;
use App\Models\DocumentationTest;
use App\Models\Product;
use App\Models\Surgery;
use App\Models\SurgeryNote;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Plan extends Component
{
    public $visit;
    public $plans = [];

    /**
     * @var \Illuminate\Support\Collection
     */
    public $tests;

    /**
     * @var \Illuminate\Support\Collection
     */
    public $investigations;

    public $operationNote;
    public $admissionNote;
    public $surgery = '';
    public $indication = '';

    public function render()
    {
        return view('livewire.admissions.plan');
    }

    public function mount()
    {
        $this->tests = collect([]);
        $this->investigations = collect([]);
    }


    public function addPrescription($data)
    {
        $this->plans[] = $data;
    }

    public function removePlanItem($id)
    {
        $this->plans = array_slice($this->plans, 0, $id) + array_slice($this->plans, $id + 1, count($this->plans));
    }

    public function savePlan()
    {
        DB::beginTransaction();
        try {
            $admission = Admission::create([
                'patient_id' => $this->visit->patient_id,
                'visit_id' => $this->visit->visit->id,
                'admittable_type' => $this->visit->visit::class,
                'admittable_id' => $this->visit->visit->id,
            ]);

            $plan = AdmissionPlan::create([
                'admission_id' => $admission->id,
                'user_id' => auth()->user()->id,
                'indication' => $this->indication,
                'note' => $this->admissionNote,
            ]);

            foreach ($this->plans as $p) {
                $plan->treatments()->create([
                    'prescriptionable_type' => Product::class,
                    'prescriptionable_id' => $p['productId'],
                    'patient_id' => $this->visit->patient_id,
                    'name' => $p['name'],
                    'dosage' => $p['dosage'],
                    'duration' => $p['duration'],
                    'requested_by' => auth()->user()->id,
                    'frequency' => $p['frequency'],
                    'route' => $p['route'],
                ]);
            }

            $this->tests->each(fn ($test) => $plan->tests()->create([
                'name' => $test['name'],
                'describable_type' => Product::class,
                'describable_id' => $test['id'],
                'patient_id' => $this->visit->patient_id,
            ]));

            $this->investigations->each(fn ($s) => $plan->scans()->create([
                'name' => $s['name'],
                'describable_type' => Product::class,
                'describable_id' => $s['id'],
                'patient_id' => $this->visit->patient_id,
                'requested_by' => auth()->user()->id,
            ]));

            if (!empty($this->surgery)) {
                $surgery = Surgery::create([
                    'procedure' => $this->surgery,
                    'admission_plan_id' => $plan->id,
                    'patient_id' => $this->visit->patient_id,
                ]);

                $note = SurgeryNote::create([
                    'user_id' => auth()->user()->id,
                    'note' => $this->operationNote,
                    'surgery_id' => $surgery->id,
                ]);
            }

            DB::commit();
            $this->redirect(route('dashboard'), true);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
        }
    }

    public function addTest($data)
    {
        if ($this->tests->doesntContain("id", '=', $data['id'])) {
            $this->tests->add($data);
        }
    }

    public function addInvestigation($data)
    {
        if ($this->investigations->doesntContain("id", "=", $data["id"])) {
            $this->investigations->add($data);
        }
    }

    public function removeTest($id) {
        $this->tests = $this->tests->filter(fn ($i) => $i['id'] != $id);
    }

    public function removeInvestigation($id) {
        $this->investigations = $this->investigations->filter(fn ($i) => $i['id'] != $id);
    }
}
