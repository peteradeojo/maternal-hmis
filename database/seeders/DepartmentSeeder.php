<?php

namespace Database\Seeders;

use App\Enums\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'id' => Department::DOC,
                'name' => 'Consultants',
                'description' => 'Medical doctors, doctors, consultants'
            ],
            [
                'id' => Department::NUR,
                'name' => 'Nursing',
                'description' => 'Nurses, matrons, midwives'
            ],
            [
                'id' => Department::REC,
                'name' => 'Records',
                'description' => 'Records, medical records, medical records officers'
            ]
        ];

        foreach ($departments as $department) {
            DB::table('departments')->updateOrInsert($department);
        }
    }
}
