<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = [
            [
                'firstname' => 'Idowu',
                'lastname' => 'Ade-Ojo',
                'department_id' => Department::DOC->value,
                'phone' => 'doctor',
                'password' => Hash::make('password'),
            ],
            [
                'firstname' => 'Moronfoluwa',
                'lastname' => 'Ade-Ojo',
                'department_id' => Department::NUR->value,
                'phone' => 'nursing',
                'password' => Hash::make('password'),
            ],
            [
                'firstname' => 'Oluwaseun',
                'lastname' => 'Ade-Ojo',
                'department_id' => Department::REC->value,
                'phone' => 'record',
                'password' => Hash::make('password')
            ],
            [
                'firstname' => 'Boluwatife',
                'lastname' => 'Ade-Ojo',
                'department_id' => Department::IT->value,
                'phone' => 'ict',
                'password' => Hash::make('password')
            ]
        ];

        foreach ($staff as $st) {
            User::updateOrCreate(['phone' => $st['phone']], $st);
        }
    }
}
