<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductCategory::updateOrCreate(['name' => 'PHARMACY'], [
            'name' => 'PHARMACY',
            'department_id' => Department::PHA->value,
        ]);
        ProductCategory::updateOrCreate(['name' => 'RECORD'], [
            'name' => 'RECORD',
            'department_id' => Department::REC->value,
        ]);
    }
}
