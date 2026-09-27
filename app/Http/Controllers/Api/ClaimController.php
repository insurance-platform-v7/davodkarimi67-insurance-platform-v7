<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ClaimRequest;
use App\Models\Tenant;
use App\Modules\Policies\Actions\CreateClaimAction;
use App\Modules\Policies\DTOs\CreateClaimDTO;
use App\Modules\Policies\Http\Resources\ClaimResource;
use Illuminate\Http\JsonResponse;

class ClaimController extends Controller
{
    public function __construct(
        private readonly CreateClaimAction $createClaimAction,
    ) {}

    public function store(ClaimRequest $request): JsonResponse
    {
        $tenant = app('tenant');

        if (! $tenant instanceof Tenant) {
            abort(500, 'Tenant context is not available.');
        }

        $validated = $request->validated();

        if (! isset($validated['policy_id']) || ! is_numeric($validated['policy_id'])) {
            abort(422, 'Policy id must be numeric.');
        }

        $claim = $this->createClaimAction->execute(
            new CreateClaimDTO(
                tenantId: $tenant->id,
                policyId: (int) $validated['policy_id'],
                data: [
                    'requested_amount' => $validated['requested_amount'],
                    'description' => $validated['description'],
                ],
            ),
        );

        return response()->json([
            'data' => new ClaimResource($claim),
        ], 201)->header('X-API-Version', 'v1');
    }
}
