<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Reinsurance\ReinsuranceReportingService;

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
