<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
    ) {}

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'policy_id' => ['required', 'integer'],
        ]);

        $payment = $this->paymentService->createPayment(
            $validated['policy_id']
        );

        return response()
            ->json([
                'success' => true,
                'payment' => $payment,
            ])
            ->header('X-API-Version', 'v1');
    }

    public function callback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_id' => ['required', 'string'],
        ]);

        $payment = $this->paymentService->markPaid(
            $validated['transaction_id'],
            $validated
        );

        return response()
            ->json([
                'success' => true,
                'payment' => $payment,
            ])
            ->header('X-API-Version', 'v1');
    }
}
