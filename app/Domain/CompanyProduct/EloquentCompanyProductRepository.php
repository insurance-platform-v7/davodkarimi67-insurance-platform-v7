<?php

namespace App\Domain\CompanyProduct;

use App\Models\CompanyProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentCompanyProductRepository implements CompanyProductRepository
{
    public function getActiveByInsuranceProduct(
        int $insuranceProductId
    ): Collection {
        return Cache::remember(
            "company_products:{$insuranceProductId}",
            now()->addMinutes(10),
            fn () => CompanyProduct::query()
                ->where('insurance_product_id', $insuranceProductId)
                ->where('is_active', true)
                ->get()
        );
    }
}
