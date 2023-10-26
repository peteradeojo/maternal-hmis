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
            ],
            [
                'id' => Department::IT->value,
                'name' => 'Information Technology',
                'description' => 'Information Technology, IT, ICT, ICT Department'
            ],
            [
                'id' => Department::LAB->value,
                'name' => 'Laboratory',
                'description' => 'Laboratory, lab, medical laboratory, medical lab'
            ],
            [
                'id' => Department::RAD->value,
                'name' => 'Radiology',
                'description' => 'Radiology, radiologist, radiographers, radiographer'
            ],
            [
                'id' => Department::PHA->value,
                'name' => 'Pharmacy',
                'description' => 'Pharmacy, drugs, medicine'
            ],
            [
                'id' => Department::DIS->value,
                'name' => 'Dispensary',
                'description' => 'Pharmacy, drugs, medicine'
            ],
            [
                'id' => Department::NHI->value,
                'name' => "Health insurance",
                'description' => 'health insurance, insurance',
            ],
        ];

        foreach ($departments as $department) {
            DB::table('departments')->updateOrInsert($department);
        }
    }
}
