<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $categories = [
            [
                'name' => 'Adult',
                'description' => 'General/Adult patients',
            ],
            [
                'name' => 'Paediatric',
                'description' => 'Children patients',
            ],
            [
                'name' => 'Antenatal',
                'description' => 'Antenatal Patients'
            ],
            [
                'name' => 'Fertility',
                'description' => 'Fertility Patients'
            ]
        ];

        foreach ($categories as $category) {
            \App\Models\PatientCategory::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
