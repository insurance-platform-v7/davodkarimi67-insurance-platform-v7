<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Audit\AuditService;
use App\Services\Policy\PolicyService;
use App\Services\Payment\PaymentService;
use App\Services\Issuance\IssuanceService;
use App\Services\Policy\PolicyWorkflowService;
use App\Services\Issuance\Contracts\IssuanceProviderInterface;
use App\Services\Issuance\Providers\InternalIssuanceProvider;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\ZarinpalPaymentGateway;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            IssuanceProviderInterface::class,
            InternalIssuanceProvider::class
        );

        $this->app->bind(
            PaymentGatewayInterface::class,
            ZarinpalPaymentGateway::class
        );

        $this->app->singleton(AuditService::class);
        $this->app->singleton(PolicyWorkflowService::class);
        $this->app->singleton(PolicyService::class);
        $this->app->singleton(PaymentService::class);
        $this->app->singleton(IssuanceService::class);
    }

    public function boot(): void
    {
    }
}
