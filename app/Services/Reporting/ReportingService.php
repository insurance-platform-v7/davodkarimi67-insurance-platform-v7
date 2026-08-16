<?php

namespace App\Services\Reporting;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Policy;

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
            return 0.0;
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
        return Payment::query()->count();
    }

    protected function successfulPayments(): int
    {
        return Payment::query()
            ->where(
                'status',
                PaymentStatus::PAID->value
            )
            ->count();
    }
}