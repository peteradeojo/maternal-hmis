<?php

namespace App\Http\Livewire\Doctor;

use App\Enums\Department;
use App\Interfaces\OperationalEvent;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddScan extends Component
{
    public $event;
    public $categoryId;

    public function mount(OperationalEvent $event)
    {
        $this->event = $event;

        $this->categoryId = ProductCategory::where('department_id', Department::RAD->value)->first()?->id;
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

            $this->event->imagings()->create([
                'describable_type' => Product::class,
                'describable_id' => $p->id,
                'patient_id' => $this->event->patient_id,
                'requested_by' => $user,
                'name' => $data['product']['name'],
            ]);

            DB::commit();
            notifyUserSuccess("Scan request added.", $user);
        } catch (\Throwable $th) {
            DB::rollBack();
            notifyUserError("Unable to add this scan.", $user);
        }

        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.doctor.add-scan');
    }
}
