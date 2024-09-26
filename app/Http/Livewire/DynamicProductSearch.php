<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;

class DynamicProductSearch extends Component
{
    public $departmentId;
    public $category;

    public $queryString;
    public $results;

    public $display = true;

    public function mount($departmentId = null, $category = null)
    {
        $this->departmentId =  $departmentId;
        $this->category = ProductCategory::where('department_id', $departmentId)->orWhere('name',  $category)->first();
    }

    public function resetResults()
    {
        $this->display = false;
    }

    public function searchProducts()
    {
        if ($this->queryString != "") {
        }
        $query = Product::query()->limit(100)->where('name', 'like',  '%' . $this->queryString  . '%');
        if ($this->category) {
            $query->where('product_category_id', $this->category->id);
        }

        if ($this->departmentId) {
            $query->whereHas('category', function ($q) {
                $q->where('department_id', $this->departmentId);
            });
        }

        $this->results = [new Product([
            'id' => null,
            'name' => "Your item: $this->queryString",
            'product_category_id' => $this->category?->id,
        ]), ...($query->get())];
        $this->display = true;
    }

    public function select($id = null)
    {
        if ($id == null) {
            $id = Product::create([
                'name' => $this->queryString,
                'description' => '',
                'product_category_id' => $this->category?->id,
                'amount'  => 0,
            ]);
            $this->dispatch('selected', id: $id->id);
            return;
        }

        $this->dispatch('selected', id: $id);
    }

    public function render()
    {
        return view('livewire.dynamic-product-search');
    }
}
