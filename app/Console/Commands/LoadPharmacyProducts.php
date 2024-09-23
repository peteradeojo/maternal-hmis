<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Console\Command;
use Spatie\SimpleExcel\SimpleExcelReader;

class LoadPharmacyProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-pharmacy-products';

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
        $phm = ProductCategory::where('name', 'PHARMACY')->firstOrFail();

        $rows = SimpleExcelReader::create(storage_path('products.xlsx'))->getRows();

        // $this->info("Loading {$rows->count()} rows");

        $rows->each(function ($row) use (&$phm) {
            $this->info($row['Product Name']);
            $product = Product::updateOrCreate([
                'name' => $row['Product Name'],
            ], [
                'product_category_id' => $phm->id,
                'name' => $row['Product Name'],
                'description' => $row['Product Name'],
                'amount' => floatval(($row['Sell Price'])),
                'is_visible' => 1,
            ]);
            $this->info("loaded at " . $product->id);
        });
    }
}
