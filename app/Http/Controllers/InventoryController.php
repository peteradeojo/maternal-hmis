<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\PurchaseOrderRequest;
use App\Models\Location;
use App\Models\StockLot;
use App\Models\StockItem;
use Illuminate\Http\Request;
use App\Models\StockItemCost;
use App\Models\StockItemPrice;
use App\Models\InventoryBalance;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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

        $locations = Location::all();
        return view('inventory.index', compact('categories', 'price_types', 'locations'));
    }

    public function getInventory(Request $request)
    {
        $query = InventoryBalance::with(['prices', 'location', 'item'])->groupBy(['item_id', 'location_id'])->selectRaw("item_id, location_id");

        return $this->dataTable($request, $query, [
            function ($query, $searchString) {
                $query->whereHas('item', function ($q) use ($searchString) {
                    $q->where('name', 'ilike', "%$searchString%")
                    ->orWhere('description', 'ilike', "%$searchString%")
                    ->orWhere('sku', 'ilike', "%$searchString%");
                });
            }
        ]);
    }

    public function createStockItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:64',
            'description' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', StockItem::CATEGORIES),
            'base_unit' => 'required|string|max:32',
            'lot_number' => 'nullable|string|max:255',
            'manufacture_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:manufacture_date',
            'quantity_received' => 'required|integer|min:1',
            'prices' => 'required|array|min:1',
            'prices.*.price_type' => 'required|string|in:' . implode(',', [
                StockItemPrice::RETAIL,
                StockItemPrice::NHIS,
                StockItemPrice::WARD,
                StockItemPrice::INTERNAL,
                StockItemPrice::PRIVATE,
            ]),
            'prices.*.price' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'is_pharmaceutical' => 'nullable|in:on',
            'requires_lot' => 'nullable|in:on',
            'si_unit' => 'nullable|string',
            'weight' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            $stockItem = StockItem::create([
                'name' => strtoupper($request->name),
                'sku' => strtoupper($request->sku),
                'description' => strtoupper($request->description),
                'category' => $request->category,
                'is_pharmaceutical' => $request->input('is_pharmaceutical') == 'on',
                'requires_lot' => $request->input('requires_lot') == 'on',
                'base_unit' => $request->base_unit,
                'si_unit' => strtolower($request->si_unit),
                'weight' => $request->weight,
            ]);

            $lot = $request->input('lot_number') !== null ? StockLot::create([
                'item_id' => $stockItem->id,
                ...$request->only(['lot_number', 'manufacture_date', 'expiry_date', 'quantity_received']),
            ]) : null;

            $t = $stockItem->transactions()->create([
                'tx_type' => StockTransaction::RECEIPT,
                'lot_id' => $lot?->id,
                'quantity' => $request->input('quantity_received'),
                'unit' => $request->input('base_unit'),
                ...$request->only(['unit_cost',]),
                'from_location_id' => 0,
                'to_location_id' => 1,
                'reason' => 'New stock',
                'performed_by' => auth()->user()->id,
            ]);

            $cost = StockItemCost::create([
                'item_id' => $stockItem->id,
                'cost' => $request->input('unit_cost'),
                'source' => StockItemCost::SOURCES['MANUAL'],
                'lot_id' => $lot?->id,
            ]);

            $prices = $request->input('prices');

            $prices = array_map(function ($price) use (&$stockItem) {
                return StockItemPrice::create([
                    'item_id' => $stockItem->id,
                    ...$price,
                    'effective_at' => now(),
                    'created_by' => auth()->user()->id,
                ]);
            }, $prices);

            DB::commit();

            return response()->json([
                'message' => 'New Stock Item created successfully',
                'data' => $stockItem,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create stock item. ' . $e->getMessage()], 500);
        }
    }

    public function viewStockDetails(Request $request, StockItem $item) {
        $item->load(['balances', 'prices', 'costs']);
        $categories = StockItem::CATEGORIES;
        return view('inventory.item-view', compact('item', 'categories'));
    }

    public function purchaseOrders(Request $request)
    {
        $data = PurchaseOrder::latest()->get();
        return view('inventory.po.index', compact('data'));
    }

    public function storePurchaseOrder(PurchaseOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $po = PurchaseOrder::create([
                'supplier_id' => $request->input('supplier_id'),
                'status' => Status::pending,
                'po_number' => PurchaseOrder::generatePoNumber(),
            ]);
            $this->saveOrderLines($po, $request->orders);

            DB::commit();

            return response()->json(['order' => $po]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function createPurchaseOrder(Request $request)
    {
        $suppliers = Supplier::all();
        return view('inventory.po.create', compact('suppliers'));
    }

    public function suppliers(Request $request)
    {
        if (!$request->isMethod('POST')) {
            $suppliers = Supplier::all();
            return view('inventory.suppliers.index', compact('suppliers'));
        }

        $request->validate([
            'name' => 'required|string|unique:suppliers,name',
            'address' => 'nullable|string',
            'tel' => 'nullable|string',
            'email' => 'nullable|string',
        ]);

        Supplier::create([
            'name' => $request->name,
            'contact' => $request->except(['name', '_token']),
        ]);

        return redirect()->to(route('phm.inventory.suppliers'));
    }

    public function viewOrder(Request $request, PurchaseOrder $order)
    {
        $order->load(['lines.item']);
        $suppliers = Supplier::all();
        return view('inventory.po.show', compact('order', 'suppliers'));
    }

    protected function saveOrderLines(PurchaseOrder &$order, array $data)
    {
        $ids = [];

        foreach ($data as $i => $line) {
            if (isset($line['id'])) { // updating
                $ids[] = $line['id'];

                PurchaseOrderLine::where('id', $line['id'])->update([
                    'qty_ordered' => $line['qty_ordered'],
                    'unit_cost' => $line['unit_cost'],
                    'qty_received' => $line['qty_received'],
                    'unit' => $line['unit'],
                ]);
            } else { // new order line
                $p_line = $order->lines()->create([
                    'item_id' => $line['item_id'],
                    'qty_ordered' => $line['qty_ordered'],
                    'unit_cost' => $line['unit_cost'],
                    'unit' => $line['unit'],
                    'qty_received' => $line['qty_received'],
                ]);

                $ids[] = $p_line->id;
                $data[$i] = $p_line->toArray();
            }
        }

        return $ids;
    }

    public function editPurchaseOrder(PurchaseOrderRequest $request, PurchaseOrder $order)
    {
        // dd($request->all());
        if ($order->status !== Status::pending) {
            return response()->json([
                'message' => "This order can no longer be modified."
            ], Response::HTTP_CONFLICT);
        }

        DB::beginTransaction();

        try {
            $lineIds = $this->saveOrderLines($order, $request->orders);

            $order->status = $request->input('status');

            if ($request->input('status') == Status::completed->value) {
                foreach ($request->orders as $line) {
                    StockTransaction::create([
                        'tx_type' => StockTransaction::RECEIPT,
                        'item_id' => $line['item_id'],
                        'quantity' => $line['qty_received'],
                        'unit' => $line['unit'],
                        'unit_cost' => $line['unit_cost'],
                        'from_location_id' => 0,
                        'to_location_id' => 1,
                        'lot_id' => null,
                        'reason' => 'Purchase order fulfilled',
                        'performed_by' => auth()->user()->id,
                    ]);

                    StockItemCost::create([
                        'item_id' => $line['item_id'],
                    ]);
                }
            }

            PurchaseOrderLine::where('po_id', $order->id)->whereNotIn('id', $lineIds)->delete();
            $order->save();

            DB::commit();
            return response()->json([
                'message' => 'Order updated successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th->getMessage());

            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function bulkImport(Request $request)
    {
        $client = Redis::client();
        if (!$request->isMethod('POST')) {
            $keys = $client->hgetall("stock-imports");
            return view('inventory.bulk-import', compact('keys'));
        }

        $request->validate([
            'import' => 'required|file|mimetypes:text/csv',
        ]);

        $file = $request->file('import');

        $path = $file->store();

        $key = date('YmdHis');

        $client->hset("stock-imports", $key, $path);
        Artisan::queue("app:bulk-stock-import", ['hkey' => $key]);

        return redirect()->back();
    }
}
