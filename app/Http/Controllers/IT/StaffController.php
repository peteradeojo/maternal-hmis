<?php

namespace App\Http\Controllers\IT;

use App\Enums\Department as EnumsDepartment;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        if ($request->method() == 'POST') {
            $data = $request->validate([
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'phone' => 'required|string',
                'department_id' => 'required|integer|exists:departments,id',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'phone' => $data['phone'],
                'department_id' => $data['department_id'],
                'password' => bcrypt($data['password']),
            ]);

            return redirect()->route('it.staff')->with('success', 'Staff added successfully');
        }

        $users = User::all();
        $departments = Department::all();
        return view('it.staff', compact('users', 'departments'));
    }

    public function show(Request $request, User $user) {
        if ($request->method() == 'POST') {
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('it.staff.view', $user)->with('success', 'Password updated successfully');
        }

        return view('it.staff-view', compact('user'));
    }
}
