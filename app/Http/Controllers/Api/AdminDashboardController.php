<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\AdminDashboardService;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected AdminDashboardService $dashboardService
    ) {}

    public function index(): JsonResponse
    {
        return response()
            ->json($this->dashboardService->getSummary())
            ->header('X-API-Version', 'v1');
    }
}
