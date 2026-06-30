<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $quotesCount = Quote::query()->count();

        $policiesCount = Policy::query()->count();

        $paymentsCount = DB::table('payments')
            ->count();

        $revenue = DB::table('payments')
            ->where('status', 'success')
            ->sum('amount');

        return response()->json([
            'quotes_count' => $quotesCount,
            'policies_count' => $policiesCount,
            'payments_count' => $paymentsCount,
            'revenue' => (float) $revenue,
        ]);
    }
}
