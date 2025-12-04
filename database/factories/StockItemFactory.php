<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockItem>
 */
class StockItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->sentence(3);
        return [
            'name' => $name,
            'sku' => fake()->unique()->lexify('phm-???-????'),
            'description' => $name,
            'category' => 'DRUG',
            'is_pharmaceutical' => true,
            'requires_lot' => false,
            'base_unit' => fake()->randomElement(['ampoule', 'box', 'cup', 'satchet', 'bottle']),
        ];
    }
}
