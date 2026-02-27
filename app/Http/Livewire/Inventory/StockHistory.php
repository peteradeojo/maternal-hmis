<?php

namespace App\Http\Livewire\Inventory;

use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockHistory extends Component
{
    public $transactions;

    public $search = '';

    public $cursor = null;

    public function mount()
    {
        $this->fetchTransactions();
    }

    public function resetCursor() {
        $this->cursor = null;
    }

    public function buildQuery($direction = ">")
    {
        $query = StockTransaction::with(['item']);

        if (!is_null($this->cursor)) {
            $query = $query->whereRaw("(created_at, id) $direction ('{$this->cursor['created_at']}', {$this->cursor['id']})");
        }

        if (!empty($this->search)) {
            $query = $query->whereHas('item', fn($q) => $q->where('name', 'ilike', "%{$this->search}%"));
        }

        $query = $query->orderBy('created_at')->orderBy('id')->limit(50);
        return $query;
    }

    public function loadTransactions() {
        $this->transactions = $this->buildQuery()->get();
    }

    private function fetchTransactions($direction = ">")
    {
        if (!is_null($this->cursor) && intval($this->transactions?->count()) > 0) {
            if ($direction == ">") {
                $this->cursor = $this->transactions->last()?->only('created_at', 'id');
            } else {
                $this->cursor = $this->transactions->first()?->only('created_at', 'id');
            }
        }

        $this->transactions = $this->buildQuery($direction)->get();
        $this->cursor = $this->transactions->last()?->only('created_at', 'id');
    }

    public function render()
    {
        return view('livewire.inventory.stock-history');
    }

    public function next()
    {
        $this->fetchTransactions(">");
    }

    public function previous()
    {
        $this->fetchTransactions("<");
    }
}
