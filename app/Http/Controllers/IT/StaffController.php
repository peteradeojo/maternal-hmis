<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::all();
        $departments = Department::all();
        return view('it.staff', compact('users', 'departments'));
    }
}
