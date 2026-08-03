<?php

namespace App\Services\Formula;

use App\Exceptions\Formula\FormulaVersionNotFoundException;
use App\Exceptions\Formula\NoActiveFormulaException;
use App\Models\CompanyProduct;
use App\Models\FormulaVersion;

class ProductFormulaResolver
{
    /**
     * Resolve active formula version for a company product.
     *
     * @throws NoActiveFormulaException
     * @throws FormulaVersionNotFoundException
     */
    public function resolve(
        CompanyProduct $companyProduct,
        FormulaVersionResolver $versionResolver
    ): FormulaVersion {

        $productFormula = $companyProduct
            ->productFormula()
            ->with('version')
            ->first();

        if (! $productFormula) {
            throw new NoActiveFormulaException;
        }

        $version = $productFormula->version;

        if (! $version) {
            $version = $versionResolver->resolve(
                $productFormula->formula_id
            );
        }

        if (! $version) {
            throw new FormulaVersionNotFoundException;
        }

        return $version;
    }
}
