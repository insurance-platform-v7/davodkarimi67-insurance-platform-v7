<?php

namespace App\Services\Reporting;

use App\Models\Policy;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    public function premiumTotal(): float
    {
        return (float) Policy::query()
            ->sum('premium');
    }

    public function policyCount(): int
    {
        return Policy::query()->count();
    }

    public function paymentSuccessRate(): float
    {
        $total = DB::table('payments')->count();

        if ($total === 0) {
            return 0;
        }

        $successful = DB::table('payments')
            ->where('status', 'success')
            ->count();

        return round(
            ($successful / $total) * 100,
            2
        );
    }

    public function summary(): array
    {
        return [
            'premium_total' => $this->premiumTotal(),
            'policy_count' => $this->policyCount(),
            'payment_success_rate' => $this->paymentSuccessRate(),
        ];
    }
}
