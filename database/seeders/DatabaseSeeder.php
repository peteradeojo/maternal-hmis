<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::updateOrCreate(['phone' => '08103490675'], [
        //     'firstname' => 'Boluwatife',
        //     'lastname' => 'Ade-Ojo',
        //     'phone' => '08103490675',
        //     'password' => Hash::make(env('DEFAULT_PASSWORD', 'password')),
        //     'department_id' => Department::IT->value,
        // ]);
        if (!App::environment('production')) {
            (new UserSeeder)->run();
        }
        (new CategorySeeder)->run();
        (new DepartmentSeeder)->run();
        (new WardSeeder)->run();
    }
}
