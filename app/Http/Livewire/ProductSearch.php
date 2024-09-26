<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductSearch extends Component
{
    public $category;
    public $department;
    public $search;
    public $results;
    public $display = false;

    public function mount($category = null, $departmentId = null)
    {
        $this->category = $category;
        $this->department = $departmentId;
    }
    public function render()
    {
        return view('livewire.product-search');
    }

    public function searchProducts()
    {
        if (empty($this->search)  || $this->search == "") {
            $this->results = null;
        }

        $this->results = Product::whereHas('category', function ($q) {
            if ($this->department) {
                $q->where('department_id', $this->department);
                return;
            }

            if ($this->category) {
                if (is_array($this->category)) {
                    $q->whereIn('name', $this->category);
                } else $q->where('name', $this->category);
            }
        })->where(function ($q) {
            $q->where('name', 'like', "%{$this->search}%")->orWhere('description', 'like', "%{$this->search}%");
        })->limit(100)->get();
        $this->display = true;
    }

    public function select($id, $name)
    {
        $this->dispatch("selected", id: $id, details: ['name' => $name, 'category' => $this->category, 'departmentId' => $this->department]);
    }

    public function resetResults()
    {
        $this->results = null;
    }
}
