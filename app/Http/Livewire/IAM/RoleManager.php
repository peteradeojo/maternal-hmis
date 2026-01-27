<?php

namespace App\Http\Livewire\IAM;

use Livewire\Component;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    public $roles;
    public $permissions;
    public $name;
    public $roleId;
    public $selectedPermissions = [];
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|unique:roles,name',
    ];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->roles = Role::with('permissions')->get();
        $this->permissions = Permission::all();
    }

    public function resetFields()
    {
        $this->name = '';
        $this->roleId = null;
        $this->selectedPermissions = [];
        $this->isEditing = false;
    }

    public function createRole()
    {
        $this->validate();

        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);

        $this->resetFields();
        $this->refreshData();
        session()->flash('message', 'Role created successfully.');
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditing = true;
    }

    public function updateRole()
    {
        $this->validate([
            'name' => 'required|string|unique:roles,name,' . $this->roleId,
        ]);

        $role = Role::findOrFail($this->roleId);
        $role->update(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);

        $this->resetFields();
        $this->refreshData();
        session()->flash('message', 'Role updated successfully.');
    }

    public function deleteRole($id)
    {
        Role::findOrFail($id)->delete();
        $this->refreshData();
        session()->flash('message', 'Role deleted successfully.');
    }

    public function render()
    {
        return view('livewire.iam.role-manager');
    }
}
