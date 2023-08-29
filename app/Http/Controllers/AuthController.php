<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string'
        ]);


        if (auth()->attempt($request->only('phone', 'password'))) {
            // $user = User::where('phone', $request->phone)->first();
            $request->session()->regenerate();

            // $token = $user->createToken('auth_token')->plainTextToken;
            // return redirect(route('dashboard'))->withCookie(cookie('auth_token', $token, 60 * 24, null, null, false, App::environment('prodcuction')));
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->back()->with('error', "Invalid login");
    }
}
