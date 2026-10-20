<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Reinsurance\ReinsuranceReportingService;
use Illuminate\Http\JsonResponse;

class ReinsuranceReportController extends Controller
{
    public function __construct(
        private readonly ReinsuranceReportingService $reportingService,
    ) {}

    public function index(): JsonResponse
    {
        return response()
            ->json($this->reportingService->summary())
            ->header('X-API-Version', 'v1');
    }
}
