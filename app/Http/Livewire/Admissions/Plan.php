<?php

namespace App\Http\Livewire\Admissions;

use App\Enums\Status;
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

    public $admission;

    public function render()
    {
        return view('livewire.admissions.plan');
    }

    public function mount($visit, $admission = null)
    {
        if (!empty($admission)) {
            $this->tests = $admission->plan->tests;
            $this->investigations = $admission->plan->scans;

            $this->plans = $admission->plan->treatments;

            $this->admission = $admission;
            $this->indication = $this->admission->plan->indication;
        } else {
            $this->tests = collect([]);
            $this->investigations = collect([]);
        }
    }


    public function addPrescription($data)
    {
        if (empty($this->admission)) {
            $this->plans[] = $data['product'];
            return;
        }

        $product = Product::find($data['product']['id']);

        if (empty($product)) {
            $product = (object) ($data['product']);
        }

        $this->admission->plan->addPrescription($this->admission->patient, $product, (object) $data, $this->admission->plan);

        $this->admission->refresh();
        $this->plans = $this->admission->plan->treatments;
    }

    public function removePlanItem($id, $index = null)
    {
        if (empty($this->admission)) {
            unset($this->plans[$id]);
            $this->plans = array_values($this->plans);
            return;
        }

        $this->admission->plan->treatments()->where('id', $index)->delete();
        $this->admission->refresh();

        $this->plans = $this->admission->plan->treatments;
        // $this->admission->plan->refresh();
    }

    public function savePlan()
    {
        if (empty($this->admission)) {
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
                    if (empty($p['productId'])) {
                        $prd = Product::create($p['product']);
                        $p['productId'] = $prd->id;
                    }

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
                        'status' => Status::active->value,
                    ]);
                }

                $this->tests->each(fn($test) => $admission->tests()->create([
                    'name' => $test['name'],
                    'describable_type' => Product::class,
                    'describable_id' => $test['id'],
                    'patient_id' => $this->visit->patient_id,
                ]));

                $this->investigations->each(fn($s) => $plan->scans()->create([
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
    }

    public function addTest($data)
    {
        if (empty($this->admission)) {
            if ($this->tests->doesntContain("id", '=', $data['id'])) {
                $this->tests->add($data);
            }

            return;
        } else {
            $this->admission->plan->tests()->firstOrCreate([
                'describable_type' => Product::class,
                'describable_id' => $data['id'],
                'patient_id' => $this->admission->patient_id,
                'name' => $data['name'],
                'status' => Status::pending->value,
            ], [
                'describable_type' => Product::class,
                'describable_id' => $data['id'],
                'patient_id' => $this->admission->patient_id,
                'name' => $data['name'],
            ]);

            $this->admission->refresh();
            $this->tests = $this->admission->plan->tests;
        }
    }

    public function addInvestigation($data)
    {
        if (empty($this->admission)) {
            if ($this->investigations->doesntContain("id", "=", $data['id'])) {
                $this->investigations->add($data);
            }
            return;
        }

        $this->admission->plan->scans()->firstOrCreate([
            'describable_type' => Product::class,
            'describable_id' => $data['id'],
            'patient_id' => $this->admission->patient_id,
            'name' => $data['name'],
            'status' => Status::pending->value,
        ], [
            'describable_type' => Product::class,
            'describable_id' => $data['id'],
            'patient_id' => $this->admission->patient_id,
            'name' => $data['name'],
            'requested_by' => auth()->user()->id,
        ]);

        $this->admission->refresh();
        $this->investigations = $this->admission->plan->scans;
    }

    public function removeTest($id)
    {
        $this->tests = $this->tests->filter(fn($i) => $i['id'] != $id);
    }

    public function removeInvestigation($id)
    {
        $this->investigations = $this->investigations->filter(fn($i) => $i['id'] != $id);
    }
}
