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
                'id' => Department::DOC->value,
                'name' => 'Consultants',
                'description' => 'Medical doctors, doctors, consultants',
                'code' => Department::DOC->name,
            ],
            [
                'id' => Department::NUR->value,
                'code' => Department::NUR->name,
                'name' => 'Nursing',
                'description' => 'Nurses, matrons, midwives'
            ],
            [
                'id' => Department::REC->value,
                'code' => Department::REC->name,
                'name' => 'Records',
                'description' => 'Records, medical records, medical records officers'
            ],
            [
                'id' => Department::IT->value,
                'code' => Department::IT->name,
                'name' => 'Information Technology',
                'description' => 'Information Technology, IT, ICT, ICT Department'
            ],
            [
                'id' => Department::LAB->value,
                'code' => Department::LAB->name,
                'name' => 'Laboratory',
                'description' => 'Laboratory, lab, medical laboratory, medical lab'
            ],
            [
                'id' => Department::RAD->value,
                'code' => Department::RAD->name,
                'name' => 'Radiology',
                'description' => 'Radiology, radiologist, radiographers, radiographer'
            ],
            [
                'id' => Department::PHA->value,
                'code' => Department::PHA->name,
                'name' => 'Pharmacy',
                'description' => 'Pharmacy, drugs, medicine'
            ],
            [
                'id' => Department::DIS->value,
                'code' => Department::DIS->name,
                'name' => 'Dispensary',
                'description' => 'Pharmacy, drugs, medicine'
            ],
            [
                'id' => Department::NHI->value,
                'code' => Department::NHI->name,
                'name' => "Health insurance",
                'description' => 'health insurance, insurance',
            ],
        ];

        foreach ($departments as $i => $department) {
            DB::table('departments')->updateOrInsert(['id' => $department['id']], $department);
        }
    }
}
