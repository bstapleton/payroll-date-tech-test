<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function check(Request $request)
    {
        return response()->json([
            'data' => [
                'status' => 'success'
            ]
        ]);
    }
}
