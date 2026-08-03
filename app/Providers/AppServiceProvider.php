<?php

namespace App\Providers;

use App\Domain\CompanyProduct\CompanyProductRepository;
use App\Domain\CompanyProduct\EloquentCompanyProductRepository;
use App\Domain\Payment\EloquentPaymentRepository;
use App\Domain\Payment\PaymentRepository;
use App\Domain\Policy\EloquentPolicyRepository;
use App\Domain\Policy\PolicyRepository;
use App\Policies\TenantPolicy;
use App\Services\Audit\AuditService;
use App\Services\Issuance\Contracts\IssuanceProviderInterface;
use App\Services\Issuance\IssuanceService;
use App\Services\Issuance\Providers\InternalIssuanceProvider;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\ZarinpalPaymentGateway;
use App\Services\Payment\PaymentService;
use App\Services\Policy\PolicyService;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

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

        $this->app->bind(
            CompanyProductRepository::class,
            EloquentCompanyProductRepository::class
        );

        $this->app->bind(
            PolicyRepository::class,
            EloquentPolicyRepository::class
        );

        $this->app->bind(
            PaymentRepository::class,
            EloquentPaymentRepository::class
        );

        $this->app->singleton(AuditService::class);
        $this->app->singleton(PolicyWorkflowService::class);
        $this->app->singleton(PolicyService::class);
        $this->app->singleton(PaymentService::class);
        $this->app->singleton(IssuanceService::class);
    }

    public function boot(): void
    {
        Gate::define(
            'tenant-access',
            [TenantPolicy::class, 'access']
        );

        Log::info('observability_ready', [
            'grafana' => true,
            'prometheus' => true,
            'sentry' => true,
            'environment' => config('app.env'),
        ]);
    }
}
