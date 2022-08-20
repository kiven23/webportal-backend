<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\RevolvingFund;

class RevolvingFundController extends Controller
{
    public function index()
    {
        return RevolvingFund::all();
    }

    public function create(Request $request)
    {
        $request->validate([
            'as_of' => 'required|date',
            'fund' => 'required',
            'cash_advances' => 'required',
        ]);

        return response()->json([
            'message' => 'New revolving fund successfully added'
        ]);
    }
}
