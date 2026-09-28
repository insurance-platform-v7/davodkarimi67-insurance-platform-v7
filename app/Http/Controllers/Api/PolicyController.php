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

        /** @var int|string $offerId */
        $offerId = $validated['offer_id'];

        $policy = $this->issuePolicyAction->execute(
            new IssuePolicyDTO(
                offerId: (int) $offerId,
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
