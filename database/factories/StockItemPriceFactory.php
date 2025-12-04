<?php

namespace Database\Factories;

use App\Models\StockItemPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockItemPrice>
 */
class StockItemPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price_type' => fake()->randomElement([
                StockItemPrice::RETAIL,
                StockItemPrice::NHIS,
                // StockItemPrice::PRIVATE,
                // StockItemPrice::INTERNAL,
                StockItemPrice::WARD,
                // StockItemPrice::WHOLESALE,
            ]),
            'price' => fake()->randomFloat(2, 10, 100000),
            'effective_at' => now(),
            'created_by' => 8,
            'active' => fake()->randomElement([true, false]),
        ];
    }
}
