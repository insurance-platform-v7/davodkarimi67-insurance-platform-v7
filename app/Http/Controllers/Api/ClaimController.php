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

        $tenantId = app('tenant')->id;

        $policy = Policy::query()
            ->whereKey($validated['policy_id'])
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        $claim = $service->create(
            $policy,
            $validated
        );

        return response()->json([
            'data' => [
                'id' => $claim->id,
                'claim_number' => $claim->claim_number,
                'policy_id' => $claim->policy_id,
                'status' => $claim->status->value,
                'requested_amount' => $claim->requested_amount,
                'description' => $claim->description,
            ],
        ], 201)->header(
            'X-API-Version',
            'v1'
        );
    }
}
