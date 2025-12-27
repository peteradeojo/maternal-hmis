<?php

namespace Database\Seeders;

use App\Enums\Department;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view patients',
            'create patients',
            'edit patients',
            'delete patients',
            'view visits',
            'create visits',
            'edit visits',
            'view admissions',
            'create admissions',
            'edit admissions',
            'view bills',
            'create bills',
            'edit bills',
            'manage users',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles and assign existing permissions
        $roles = [
            'admin' => $permissions,
            'doctor' => [
                'view patients',
                'edit patients',
                'view visits',
                'create visits',
                'edit visits',
                'view admissions',
                'create admissions',
                'edit admissions',
            ],
            'nurse' => [
                'view patients',
                'view visits',
                'view admissions',
                'edit admissions',
            ],
            'record' => [
                'view patients',
                'create patients',
                'edit patients',
                'view visits',
                'create visits',
            ],
            'pharmacy' => [
                'view patients',
                'view visits',
                'view bills',
            ],
            'lab' => [
                'view patients',
                'view visits',
            ],
            'radiology' => [
                'view patients',
                'view visits',
            ],
            'billing' => [
                'view patients',
                'view visits',
                'view bills',
                'create bills',
                'edit bills',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions($rolePermissions);
        }
    }
}
