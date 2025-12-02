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

    public $display = false;

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
        if (strlen($this->queryString) == 0) return;
        $query = Product::query()->where('is_visible', 1)->limit(100)->where(function ($q) {
            $q->where('name', 'like', '%' . $this->queryString  . '%')->orWhere('description', 'like', "%$this->queryString%");
        });

        if ($this->departmentId) {
            $query->whereHas('category', function ($q) {
                $q->where('department_id', $this->departmentId);
            });
        }

        // $this->results = [new Product([
        //     'id' => null,
        //     'name' => "Your item: $this->queryString",
        //     'product_category_id' => $this->category?->id,
        // ]), ...($query->get())];
        $this->results = $query->get();
        $this->display = true;
    }

    public function select(?Product $product)
    {
        $this->resetResults();

        $id = @$product['id'];
        if ($id == null) {
            if (empty($this->queryString)) return;
            $product = new Product([
                'name' => $this->queryString,
                'description' => '',
                'product_category_id' => $this->category?->id,
                'amount'  => 0,
            ]);

            $this->dispatch('selected_temp', product: $product, name: $product->name, id: null);
            return;
        }

        $this->reset('queryString', 'results');

        $this->dispatch('selected', id: $id, name: $product['name'], product: $product);
    }

    public function render()
    {
        return view('livewire.dynamic-product-search');
    }
}
