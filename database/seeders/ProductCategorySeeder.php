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
        $data = [
            [
                'name' => 'LABORATORY',
                'department_id' => Department::LAB->value,
            ],
            [
                'name' => 'PHARMACY',
                'department_id' => Department::PHA->value,
            ],
            [
                'name' => 'RECORD',
                'department_id' => Department::REC->value,
            ],
            [
                'name' => 'RADIOLOGY',
                'department_id' => Department::RAD->value,
            ]
        ];

        foreach ($data as $row) {
            ProductCategory::updateOrCreate(['name' => $row['name']], $row);
        }
    }
}
