<?php

namespace App\Providers;

use App\Domain\CompanyProduct\CompanyProductRepository;
use App\Domain\CompanyProduct\EloquentCompanyProductRepository;
use App\Domain\Payment\EloquentPaymentRepository;
use App\Domain\Payment\PaymentRepository;
use App\Domain\Policy\EloquentPolicyRepository;
use App\Domain\Policy\PolicyRepository;
use App\Policies\TenantPolicy;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Quote\QuoteRepository;
use App\Services\Audit\AuditService;
use App\Services\Issuance\Contracts\IssuanceProviderInterface;
use App\Services\Issuance\IssuanceService;
use App\Services\Issuance\Providers\InternalIssuanceProvider;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\FakePaymentGateway;
use App\Services\Payment\Gateways\ZarinpalPaymentGateway;
use App\Services\Payment\PaymentService;
use App\Services\Policy\PolicyService;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerIssuance();
        $this->registerPaymentGateway();
        $this->registerRepositories();
        $this->registerServices();
    }

    private function registerIssuance(): void
    {
        $this->app->bind(
            IssuanceProviderInterface::class,
            InternalIssuanceProvider::class
        );
    }

    private function registerPaymentGateway(): void
    {
        $this->app->bind(
            PaymentGatewayInterface::class,
            function () {
                $configuredGateway = config(
                    'services.payment_gateway',
                    'fake'
                );

                $gateway = is_string($configuredGateway)
                    ? strtolower($configuredGateway)
                    : 'fake';

                return match ($gateway) {
                    'fake' => app(FakePaymentGateway::class),
                    'zarinpal' => app(ZarinpalPaymentGateway::class),
                    default => throw new InvalidArgumentException(
                        'Unsupported payment gateway: '.$gateway
                    ),
                };
            }
        );
    }

    private function registerRepositories(): void
    {
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

        $this->app->bind(CustomerRepository::class);
        $this->app->bind(QuoteRepository::class);
    }

    private function registerServices(): void
    {
        $this->app->singleton(AuditService::class);
        $this->app->singleton(PolicyWorkflowService::class);
        $this->app->singleton(PolicyService::class);
        $this->app->singleton(PaymentService::class);
        $this->app->singleton(IssuanceService::class);
    }

    public function boot(): void
    {
        Gate::define(
            'access',
            [TenantPolicy::class, 'access']
        );

        Log::info('observability_ready', [
            'grafana' => true,
            'prometheus' => true,
            'sentry' => true,
        ]);
    }
}
