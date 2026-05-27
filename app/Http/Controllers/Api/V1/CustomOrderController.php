<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomOrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'min:2', 'max:100'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();

        CustomOrder::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Your request has been received. We will reach out to you shortly.',
        ], 201);
    }
}
