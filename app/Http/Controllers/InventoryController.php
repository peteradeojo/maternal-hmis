<?php

namespace App\Http\Controllers;

use App\Models\InventoryBalance;
use App\Models\StockItem;
use App\Models\StockItemPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = StockItem::CATEGORIES;
        $price_types = [
            StockItemPrice::RETAIL,
            StockItemPrice::NHIS,
            StockItemPrice::WARD,
            StockItemPrice::INTERNAL,
            StockItemPrice::PRIVATE,
        ];
        return view('inventory.index', compact('categories', 'price_types'));
    }

    public function getInventory(Request $request)
    {
        $query = InventoryBalance::with(['prices', 'location']);
        return $this->dataTable($request, $query, []);
    }
}
