<?php

namespace App\Console\Commands;

use App\Models\StockItem;
use App\Models\StockItemCost;
use App\Models\StockItemPrice;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class BulkStockImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bulk-stock-import {hkey}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hkey = $this->argument("hkey");
        $filename = Redis::client()->hget("stock-imports", $hkey);

        if (!$filename) {
            return;
        }

        $filename = storage_path("app/$filename");
        $fh = fopen($filename, 'r');

        DB::transaction(function () use (&$fh) {
            $user = User::where('phone', 'ict')->first();
            $header = fgetcsv($fh);
            while ($data = fgetcsv($fh)) {
                $item = collect(array_combine($header, $data));

                $sku = $item->get('sku');
                $weight = $item->get('weight', null);
                empty($weight) && $weight = null;
                empty($sku) && $sku = "INV_" . rand(100000, 999999);

                $stockItem = StockItem::create([
                    'sku' => $sku,
                    ...$item->only([
                        'base_unit',
                        'name',
                        'si_unit',
                        'is_pharmaceutical',
                        'category',
                        'description',
                    ]),
                    'weight' => $weight,
                ]);

                $stockItem->transactions()->create([
                    'tx_type' => StockTransaction::RECEIPT,
                    'quantity' => $item->get('quantity'),
                    'from_location_id' => 0,
                    'to_location_id' => 1,
                    'performed_by' => $user->id,
                    'unit_cost' => $item->get('unit_cost'),
                    'unit' => $stockItem->base_unit,
                    'reason' => "New stock: import",
                ]);

                $stockItem->costs()->create([
                    'cost' => $item->get('unit_cost'),
                    'source' => StockItemCost::SOURCES['MANUAL'],
                ]);

                $stockItem->prices()->create([
                    'price_type' => StockItemPrice::RETAIL,
                    'currency' => 'NGN',
                    'active' => true,
                    'effective_at' => now(),
                    'price' => $item->get('price'),
                    'created_by' => $user->id,
                ]);
            }
        });

        fclose($fh);
        unlink($filename);

        Redis::client()->hdel("stock-imports", $hkey);
    }
}
