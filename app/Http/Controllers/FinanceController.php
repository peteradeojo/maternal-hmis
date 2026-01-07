<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        return view('finance.index');
    }
}
