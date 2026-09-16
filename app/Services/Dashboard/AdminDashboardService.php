<?php

namespace App\Services\Dashboard;

use App\Models\Policy;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    /**
     * @return array<string, int|float>
     */
    public function getSummary(): array
    {
        $quotesCount = Quote::query()->count();
        $policiesCount = Policy::query()->count();
        $paymentsCount = DB::table('payments')->count();
        $revenue = DB::table('payments')
            ->sum('amount');

        return [
            'quotes_count' => $quotesCount,
            'policies_count' => $policiesCount,
            'payments_count' => $paymentsCount,
            'revenue' => (float) $revenue,
        ];
    }
}