<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'policy_id' => ['required', 'integer'],
        ]);

        $payment = $this->paymentService->createPayment($validated['policy_id']);

        return response()->json([
            'success' => true,
            'payment' => $payment,
        ]);
    }

    public function callback(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => ['required', 'string'],
        ]);

        $payment = $this->paymentService->markPaid(
            $validated['transaction_id'],
            $request->all()
        );

        return response()->json([
            'success' => true,
            'payment' => $payment,
        ]);
    }
}
