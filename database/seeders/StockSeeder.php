<?php

namespace Database\Seeders;

use App\Models\StockItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $item = StockItem::factory()->count(50)->hasBalance(1)->hasPrices(3)->create();

        // dump($item);
    }
}
