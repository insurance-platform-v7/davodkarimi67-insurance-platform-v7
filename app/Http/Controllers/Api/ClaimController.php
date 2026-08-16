<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Services\Claim\ClaimService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function store(
        Request $request,
        ClaimService $service
    ): JsonResponse {
        $validated = $request->validate([
            'policy_id' => [
                'required',
                'integer',
                'exists:policies,id',
            ],

            'requested_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'description' => [
                'required',
                'string',
                'min:5',
            ],
        ]);

        $policy = Policy::findOrFail(
            $validated['policy_id']
        );

        $claim = $service->create(
            $policy,
            $validated
        );

        return response()->json([
            'data' => $claim,
        ], 201);
    }
}