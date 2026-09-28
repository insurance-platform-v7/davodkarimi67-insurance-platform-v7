<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentRequest;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}

    public function create(
        CreatePaymentRequest $request
    ): JsonResponse {
        $policyId = $request->validated('policy_id');

        $payment = $this->paymentService->createPayment(
            (int) $policyId
        );

        return response()
            ->json([
                'success' => true,
                'payment_id' => $payment->id,
            ])
            ->header('X-API-Version', 'v1');
    }
}
