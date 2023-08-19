<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string'
        ]);


        if (auth()->attempt($request->only('phone', 'password'))) {
            $user = User::where('phone', $request->phone)->first();
            auth()->login($user);

            return redirect(route('dashboard'));
        }

        return redirect()->back()->with('error', "Invalid login");
    }
}
