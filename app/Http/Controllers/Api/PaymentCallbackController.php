<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentCallbackRequest;
use App\Services\Payment\PaymentCallbackWorkflowService;
use Illuminate\Http\JsonResponse;

class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly PaymentCallbackWorkflowService $workflow
    ) {}

    public function handle(
        PaymentCallbackRequest $request
    ): JsonResponse {

        $this->workflow->handle(
            $request->validated()
        );

        return response()->json([
            'success' => true,
        ]);
    }
}
