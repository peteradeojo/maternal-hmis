<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        return view('dashboard', compact('user'));
    }

    public function profile(Request $request)
    {
        $user = auth()->user();

        return view('profile', compact('user'));
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->back()->with('success', 'Password changed successfully');
        } else {
            return redirect()->back()->with('error', 'Current password is incorrect');
        }
    }
}
