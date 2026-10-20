<?php

use App\Modules\FormulaEngine\Providers\FormulaEngineModuleServiceProvider;
use App\Modules\Issuance\Providers\IssuanceModuleServiceProvider;
use App\Modules\Notifications\Providers\NotificationsModuleServiceProvider;
use App\Modules\Payments\Providers\PaymentsModuleServiceProvider;
use App\Modules\Policies\Providers\PoliciesModuleServiceProvider;
use App\Modules\Products\Providers\ProductsModuleServiceProvider;
use App\Modules\Quotes\Providers\QuotesModuleServiceProvider;
use App\Modules\Reports\Providers\ReportsModuleServiceProvider;
use App\Modules\Users\Providers\UsersModuleServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\QuoteServiceProvider;

return [
    AppServiceProvider::class,
    QuoteServiceProvider::class,
    QuoteServiceProvider::class,
    FormulaEngineModuleServiceProvider::class,
    IssuanceModuleServiceProvider::class,
    NotificationsModuleServiceProvider::class,
    PaymentsModuleServiceProvider::class,
    PoliciesModuleServiceProvider::class,
    ProductsModuleServiceProvider::class,
    QuotesModuleServiceProvider::class,
    ReportsModuleServiceProvider::class,
    UsersModuleServiceProvider::class,
];
