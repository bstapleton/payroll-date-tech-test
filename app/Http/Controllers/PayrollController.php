<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollCheckRequest;
use Illuminate\Http\JsonResponse;

class PayrollController extends Controller
{
    public function check(PayrollCheckRequest $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'status' => 'success'
            ]
        ]);
    }
}
