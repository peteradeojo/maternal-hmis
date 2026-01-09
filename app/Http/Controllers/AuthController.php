<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string'
        ]);

        Session::flush();

        try {
            if (auth()->attempt($request->only('phone', 'password'))) {
                if (in_array($request->input('phone'), config('app.generic_doctor_profiles'))) {
                    return redirect()->route('whoami');
                }

                $request->session()->regenerate(destroy: true);
                return redirect()->intended(route('dashboard'));
            }
        } catch (Exception $e) {
            return abort(500);
        }

        return redirect()->back()->with('error', "Invalid login");
    }

    function whoami(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return view('whoami');
        }

        $request->validate([
            'whoami' => 'required|string',
        ]);

        Session::put(
            config('app.generic_doctor_id'),
            $request->input('whoami')
        );

        return redirect()->route('dashboard');
    }
}
