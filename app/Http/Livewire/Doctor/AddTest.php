<?php

namespace App\Http\Livewire\Doctor;

use App\Models\Product;
use Livewire\Component;
use App\Enums\Department;
use App\Enums\Status;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Interfaces\OperationalEvent;

class AddTest extends Component
{
    public function render()
    {
        return view('livewire.doctor.add-test');
    }

    public $event;
    public $categoryId;

    public function mount(OperationalEvent $event)
    {
        $this->event = $event;
        $this->categoryId = ProductCategory::where('department_id', Department::LAB->value)->first()?->id;
    }

    public function save($data)
    {
        $user = request()->user()->id;
        DB::beginTransaction();
        try {
            $p = Product::firstOrCreate([
                'name' => trim($data['product']['name']),
                'product_category_id' => $this->categoryId,
            ], [
                'product_category_id' => $this->categoryId,
                'description' => null,
                'amount' => 0,
                'is_visible' => true,
            ]);

            $this->event->tests()->create([
                'describable_type' => Product::class,
                'describable_id' => $p->id,
                'patient_id' => $this->event->patient->id,
                'name' => $data['product']['name'],
                'status_id' => Status::pending->value,
            ]);

            DB::commit();
            notifyUserSuccess("Scan request added.", $user);
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);
            dump($th);
            notifyUserError("Unable to add this scan.", $user);
        }

        $this->dispatch('$refresh');
        $this->dispatch('tests-added');
    }
}
