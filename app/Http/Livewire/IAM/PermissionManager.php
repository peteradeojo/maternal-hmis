<?php

namespace App\Http\Livewire\IAM;

use Livewire\Component;

use Spatie\Permission\Models\Permission;

class PermissionManager extends Component
{
    public $permissions;
    public $name;

    protected $rules = [
        'name' => 'required|string|unique:permissions,name',
    ];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->permissions = Permission::all();
    }

    public function createPermission()
    {
        $this->validate();

        Permission::create(['name' => $this->name]);

        $this->name = '';
        $this->refreshData();
        session()->flash('message', 'Permission created successfully.');
    }

    public function deletePermission($id)
    {
        Permission::findOrFail($id)->delete();
        $this->refreshData();
        session()->flash('message', 'Permission deleted successfully.');
    }

    public function render()
    {
        return view('livewire.iam.permission-manager');
    }
}
