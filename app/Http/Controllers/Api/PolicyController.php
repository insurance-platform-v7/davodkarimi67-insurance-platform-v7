<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IssuePolicyRequest;
use App\Modules\Policies\Actions\IssuePolicyAction;
use App\Modules\Policies\DTOs\IssuePolicyDTO;
use App\Modules\Policies\Http\Resources\PolicyResource;
use Illuminate\Http\JsonResponse;

class PolicyController extends Controller
{
    public function __construct(
        private readonly IssuePolicyAction $issuePolicyAction,
    ) {}

    public function issue(IssuePolicyRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (! isset($validated['offer_id']) || ! is_numeric($validated['offer_id'])) {
            abort(422, 'Offer id must be numeric.');
        }

        $policy = $this->issuePolicyAction->execute(
            new IssuePolicyDTO(
                offerId: (int) $validated['offer_id'],
            ),
        );

        return response()->json(
            [
                'data' => new PolicyResource($policy),
            ],
            201,
        )->header('X-API-Version', 'v1');
    }
}
