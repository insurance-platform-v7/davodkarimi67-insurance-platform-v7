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
        $payment = $this->paymentService->createPayment(
            $request->validated('policy_id')
        );

        return response()
            ->json([
                'success' => true,
                'payment' => $payment,
            ])
            ->header('X-API-Version', 'v1');
    }
}
