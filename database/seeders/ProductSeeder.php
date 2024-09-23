<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phmCat = ProductCategory::where('name', 'PHARMACY')->first();

        $products = [
            [
                'name' => 'Paracetamol tab 500mg',
                'description' => 'pcm, tab pcm, pcm tab, paracetamol tablets',
                'amount' => 0.00,
                'product_category_id' => $phmCat->id,
            ]
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['name' => $product['name']], $product);
        }
    }
}
