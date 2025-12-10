<?php

namespace App\Http\Livewire;

use App\Models\PurchaseOrderLine;
use App\Models\StockItem;
use Livewire\Component;

class InventoryProductSearch extends Component
{
    public $category;
    public $queryString;
    public $results;

    public function search()
    {
        // if (strlen($this->queryString) <= 1) {
        //     $this->reset('results');
        //     return;
        // }

        $query = StockItem::where('name', 'ilike', "%{$this->queryString}%")->orWhere('description', 'ilike', "%{$this->queryString}%")->limit(30);

        if ($this->category) {
            $query = $query->where('category', $this->category);
        }

        $this->results = $query->get();

        if ($this->results->isEmpty()) {
            $this->results = [new StockItem([
                'name' => $this->queryString,
                'weight' => null,
                'si_unit' => null,
            ])];
        }

        $this->dispatch('searched', count($this->results));
    }

    public function render()
    {
        return view('livewire.inventory-product-search');
    }

    public function selected($id)
    {
        if (!empty($id)) {
            $product = StockItem::find($id)?->load(['costs', 'prices']);
        } else {
            $product = new StockItem([
                'name' => $this->queryString,
            ]);
        }

        $line = new PurchaseOrderLine();
        $line->unit = $product->base_unit;
        $line->unit_cost = $product->costs->first()?->cost;
        $line->item()->associate($product);

        if ($product) {
            $this->dispatch('handle-select', product: $line);
        }
    }
}
