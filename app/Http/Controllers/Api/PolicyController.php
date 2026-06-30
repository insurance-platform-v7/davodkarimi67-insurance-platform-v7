<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Policy\PolicyService;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function __construct(
        private PolicyService $policyService
    ) {
    }

    public function issue(Request $request)
    {
        $validated = $request->validate([
            'offer_id' => ['required', 'integer'],
        ]);

        $policy = $this->policyService->issueFromOffer($validated['offer_id']);

        return response()->json([
            'success' => true,
            'policy' => $policy,
        ]);
    }
}
