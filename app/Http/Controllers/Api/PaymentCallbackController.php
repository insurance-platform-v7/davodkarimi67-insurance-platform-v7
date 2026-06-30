<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentCallbackWorkflowService;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly PaymentCallbackWorkflowService $workflow
    ) {
    }

    public function handle(Request $request)
    {
        $validated = $request->validate([
            'authority' => ['required', 'string'],
        ]);

        $this->workflow->handle($validated['authority']);

        return response()->json([
            'success' => true,
        ]);
    }
}
