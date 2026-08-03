<?php

namespace App\Services\Reporting;

use App\Models\Policy;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    public function premiumTotal(): float
    {
        return (float) Policy::query()->sum('premium');
    }

    public function policyCount(): int
    {
        return Policy::query()->count();
    }

    public function paymentSuccessRate(): float
    {
        $total = $this->totalPayments();

        if ($total === 0) {
            return 0;
        }

        return round(
            ($this->successfulPayments() / $total) * 100,
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

    protected function totalPayments(): int
    {
        return DB::table('payments')->count();
    }

    protected function successfulPayments(): int
    {
        return DB::table('payments')
            ->where('status', 'success')
            ->count();
    }
}
