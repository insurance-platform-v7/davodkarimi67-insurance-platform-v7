<?php

// File: app/Http/Controllers/Api/IssuanceController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Issuance\IssuanceService;

class IssuanceController extends Controller
{
    public function __construct(
        private IssuanceService $issuanceService
    ) {}

    public function issue(int $policyId)
    {
        return response()->json(
            $this->issuanceService->issue($policyId)
        );
    }
}
