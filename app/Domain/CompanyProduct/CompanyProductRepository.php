<?php

namespace App\Domain\CompanyProduct;

use Illuminate\Support\Collection;

interface CompanyProductRepository
{
    public function getActiveByInsuranceProduct(
        int $insuranceProductId
    ): Collection;
}
