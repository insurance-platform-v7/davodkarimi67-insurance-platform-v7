<?php

namespace App\Domain\CompanyProduct;

use App\Models\CompanyProduct;
use Illuminate\Support\Collection;

class EloquentCompanyProductRepository implements CompanyProductRepository
{
    public function getActiveByInsuranceProduct(
        int $insuranceProductId,
        ?int $tenantId
    ): Collection {
        return CompanyProduct::query()
            ->where('insurance_product_id', $insuranceProductId)
            ->where('is_active', true)
            ->where('tenant_id', $tenantId)
            ->get();
    }
}
