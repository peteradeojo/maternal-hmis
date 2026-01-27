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
        (new RoleAndPermissionSeeder)->run();
        (new CategorySeeder)->run();
        (new DepartmentSeeder)->run();
        (new WardSeeder)->run();
        if (!App::environment('production')) {
            (new UserSeeder)->run();
        }
    }
}
