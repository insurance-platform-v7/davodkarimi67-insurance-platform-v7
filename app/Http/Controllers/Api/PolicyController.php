<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Policy\PolicyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function __construct(
        private readonly PolicyService $policyService,
    ) {}

    public function issue(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validate([
            'offer_id' => [
                'required',
                'integer',
            ],
        ]);

        $offerId = $validated['offer_id'] ?? null;

        if (! is_int($offerId) && ! is_numeric($offerId)) {
            return response()->json([
                'message' => 'Invalid offer_id.',
            ], 422);
        }

        $this->policyService->issueFromOffer(
            (int) $offerId,
        );

        return response()
            ->json([
                'success' => true,
            ])
            ->header('X-API-Version', 'v1');
    }
}
