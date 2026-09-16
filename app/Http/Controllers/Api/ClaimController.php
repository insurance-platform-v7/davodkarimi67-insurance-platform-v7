<?php

namespace App\Http\Controllers\Api;

use App\Domain\Policy\PolicyRepository;
use App\Http\Controllers\Controller;
use App\Services\Claim\ClaimService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function store(
        Request $request,
        ClaimService $service,
        PolicyRepository $policyRepository
    ): JsonResponse {
        /** @var array<string, mixed> $validated */
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

        $policyId = $validated['policy_id'] ?? null;

        if (! is_int($policyId) && ! is_numeric($policyId)) {
            return response()->json([
                'message' => 'Invalid policy_id.',
            ], 422);
        }

        $tenant = app('tenant');

        if (! is_object($tenant) || ! isset($tenant->id)) {
            return response()->json([
                'message' => 'Tenant context is not available.',
            ], 500);
        }

        $tenantId = $tenant->id;

        if (! is_int($tenantId) && ! is_numeric($tenantId)) {
            return response()->json([
                'message' => 'Invalid tenant ID.',
            ], 500);
        }

        $policy = $policyRepository->findForTenantOrFail(
            (int) $policyId,
            (int) $tenantId,
        );

        /** @var array<string, mixed> $claimData */
        $claimData = $validated;

        $claim = $service->create(
            $policy,
            $claimData,
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
