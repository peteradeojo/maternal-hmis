<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Http\Controllers\LabController;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AntenatalTestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labCat = ProductCategory::where('name', 'OTHER TESTS')->firstOrCreate([
            'name' => 'OTHER TESTS',
            'department_id' => Department::LAB->value,
        ]);

        foreach (LabController::$ancBookingTests as $test) {
            Product::firstOrCreate(
                [
                    'name' => $test,
                    'product_category_id' => $labCat->id,
                ],
                [
                    'name' => $test,
                    'product_category_id' => $labCat->id,
                    'amount' => 0,
                ],
            );
        }
    }
}
