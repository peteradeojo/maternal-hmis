<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\Permissions;
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
        $permissions = Permissions::cases();

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission->value);
        }

        // Create roles and assign existing permissions
        $roles = [
            'admin' => array_map(fn($p) => $p->value, $permissions),
            'doctor' => [
                Permissions::VIEW_PATIENTS,
                Permissions::VIEW_VISITS,
                Permissions::EDIT_VISITS,
                Permissions::CREATE_VISITS,
                Permissions::EDIT_NOTES,
                Permissions::DELETE_NOTE,
                Permissions::CREATE_ADMISSIONS,
                Permissions::VIEW_ADMISSIONS,
                Permissions::EDIT_ADMISSIONS,
            ],
            'nurse' => [
                Permissions::VIEW_ADMISSIONS,
                Permissions::EDIT_ADMISSIONS,
                Permissions::VIEW_PATIENTS,
                Permissions::VIEW_VISITS,
            ],
            'record' => [
                Permissions::VIEW_PATIENTS,
                Permissions::CREATE_PATIENTS,
                Permissions::EDIT_PATIENTS,
                Permissions::VIEW_VISITS,
                Permissions::CREATE_VISITS,
            ],
            'pharmacy' => [
                Permissions::VIEW_PATIENTS,
                Permissions::VIEW_VISITS,
                Permissions::VIEW_BILLS,
                Permissions::MANAGE_PRESCRIPTIONS,
            ],
            'lab' => [
                Permissions::MANAGE_TESTS,
                Permissions::ORDER_TEST,
                Permissions::VIEW_PATIENTS,
                Permissions::VIEW_VISITS,
            ],
            'radiology' => [
                Permissions::MANAGE_SCANS,
            ],
            'billing' => [
                Permissions::VIEW_BILLS,
                Permissions::CREATE_BILLS,
                Permissions::EDIT_BILLS,
                Permissions::VIEW_VISITS,
            ],
            'media' => [
                Permissions::VIEW_POSTS,
                Permissions::EDIT_POSTS,
                Permissions::CREATE_POSTS,
            ],
            'insurance' => [
                Permissions::VIEW_PATIENTS,
                Permissions::VIEW_VISITS,
                Permissions::VIEW_BILLS,
            ],
            'support' => [],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions($rolePermissions);
        }
    }
}
