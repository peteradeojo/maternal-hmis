<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\StockItem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'id' => 0,
                'code' => 'SOURCE',
                'name' => 'SOURCE',
            ],
            [
                'id' => 1,
                'code' => 'PHARMACY',
                'name' => 'Pharmacy',
            ]
        ];

        foreach($locations as $loc) {
            Location::updateOrCreate($loc, $loc);
        }


        // $item = StockItem::factory()->count(50)->hasBalance(1)->hasPrices(3)->create();

        // dump($item);
    }
}
