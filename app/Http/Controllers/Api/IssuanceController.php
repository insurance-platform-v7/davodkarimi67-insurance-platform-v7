<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Issuance\IssuanceService;
use Illuminate\Http\JsonResponse;

class IssuanceController extends Controller
{
    public function __construct(
        private readonly IssuanceService $issuanceService
    ) {}

    public function issue(int $policyId): JsonResponse
    {
        return response()->json(
            $this->issuanceService->issue($policyId)
        )->header(
            'X-API-Version',
            'v1'
        );
    }
}
