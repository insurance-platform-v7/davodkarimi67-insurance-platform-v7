<?php

namespace App\Domain\CompanyProduct;

use App\Models\CompanyProduct;
use Illuminate\Support\Collection;

interface CompanyProductRepository
{
    /**
     * @return Collection<int, CompanyProduct>
     */
    public function getActiveByInsuranceProduct(
        int $insuranceProductId,
        ?int $tenantId
    ): Collection;
}
