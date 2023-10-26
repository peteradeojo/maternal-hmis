<?php

namespace App\Http\Livewire\It;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Departments extends Component
{
    public Collection $data;

    public function mount() {
        $this->data = Department::all();
    }
    public function render()
    {
        return view('livewire.it.departments');
    }
}
