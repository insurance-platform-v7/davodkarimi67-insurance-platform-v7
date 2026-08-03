<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Reinsurance\ReinsuranceReportingService;
use Illuminate\Http\JsonResponse;

class ReinsuranceReportController extends Controller
{
    public function index(
        ReinsuranceReportingService $service
    ): JsonResponse {

        return response()->json(
            $service->summary()
        );
    }
}
