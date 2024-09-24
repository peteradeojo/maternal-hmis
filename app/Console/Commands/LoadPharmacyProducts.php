<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class LoadPharmacyProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-products {category} {path} {type} {--delete=false}';

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
        $category = $this->argument("category");
        $phm = ProductCategory::where('name', $category)->firstOrFail();

        $rows = SimpleExcelReader::create($this->argument('path'), $this->argument('type'))->getRows();

        $rows->each(function ($row) use (&$phm) {
            $this->info($row['Product Name']);
            $product = Product::updateOrCreate([
                'name' => $row['Product Name'],
                'product_category_id' => $phm->id,
            ], [
                'product_category_id' => $phm->id,
                'name' => $row['Product Name'],
                'description' => $row['Product Name'],
                'amount' => floatval(($row['Sell Price'])),
                'is_visible' => 1,
            ]);
            $this->info("loaded at " . $product->id);
        });

        if ($this->option('delete')  === true) {
            Storage::delete($this->argument('path'));
        }
    }
}
