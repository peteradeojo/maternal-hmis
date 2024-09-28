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
        $this->category = ProductCategory::where('name', 'category')->orWhere('department_id',  $departmentId)->first();
    }

    public function resetResults()
    {
        $this->display = false;
    }

    public function searchProducts()
    {
        $query = Product::query()->limit(100)->where(function ($q) {
            $q->where('name', 'like', '%' . $this->queryString  . '%')->orWhere('description', 'like', "%$this->queryString%");
        });

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

    public function select($product)
    {
        $this->resetResults();
        $id = @$product['id'];
        if ($id == null) {
            if (empty($this->queryString)) return;
            $id = Product::create([
                'name' => $this->queryString,
                'description' => '',
                'product_category_id' => $this->category?->id,
                'amount'  => 0,
            ]);
            $this->dispatch('selected', id: $id->id, name: $id->name);
            return;
        }

        $this->dispatch('selected', id: $id, name: $product['name']);
    }

    public function render()
    {
        return view('livewire.dynamic-product-search');
    }
}
