<?php

namespace Tests\Feature;

use App\Exceptions\Formula\FormulaVersionNotFoundException;
use App\Exceptions\Formula\NoActiveFormulaException;
use App\Models\CompanyProduct;
use App\Models\Formula;
use App\Models\FormulaVersion;
use App\Models\InsuranceCompany;
use App\Models\ProductFormula;
use App\Services\Formula\FormulaVersionResolver;
use App\Services\Formula\ProductFormulaResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFormulaResolverTest extends TestCase
{
    use RefreshDatabase;

    private function createCompanyProduct(): CompanyProduct
    {
        $company = InsuranceCompany::factory()->create();

        return CompanyProduct::factory()->create([
            'insurance_company_id' => $company->id,
        ]);
    }

    private function createProductFormula(
        CompanyProduct $companyProduct,
        Formula $formula,
        ?int $versionId
    ): ProductFormula {
        return ProductFormula::create([
            'tenant_id' => $companyProduct->tenant_id,
            'insurance_product_id' => $companyProduct->insurance_product_id,
            'insurance_company_id' => $companyProduct->insurance_company_id,
            'formula_id' => $formula->id,
            'formula_version_id' => $versionId,
            'name' => 'Test Formula',
            'version' => '1.0.0',
            'formula_json' => ['premium' => 100],
            'is_active' => true,
        ]);
    }

    public function test_resolve_returns_product_formula_version(): void
    {
        $companyProduct = $this->createCompanyProduct();
        $formula = Formula::factory()->create();

        $version = FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
            'version' => 1,
            'is_active' => true,
        ]);

        $this->createProductFormula(
            $companyProduct,
            $formula,
            $version->id
        );

        $result = app(ProductFormulaResolver::class)->resolve(
            $companyProduct,
            app(FormulaVersionResolver::class)
        );

        $this->assertSame($version->id, $result->id);
    }

    public function test_resolve_falls_back_to_version_resolver(): void
    {
        $companyProduct = $this->createCompanyProduct();
        $formula = Formula::factory()->create();

        $version = FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
            'version' => 2,
            'is_active' => true,
        ]);

        $this->createProductFormula(
            $companyProduct,
            $formula,
            null
        );

        $result = app(ProductFormulaResolver::class)->resolve(
            $companyProduct,
            app(FormulaVersionResolver::class)
        );

        $this->assertSame($version->id, $result->id);
    }

    public function test_resolve_throws_when_no_product_formula_exists(): void
    {
        $companyProduct = $this->createCompanyProduct();

        $this->expectException(NoActiveFormulaException::class);

        app(ProductFormulaResolver::class)->resolve(
            $companyProduct,
            app(FormulaVersionResolver::class)
        );
    }

    public function test_resolve_throws_when_version_cannot_be_resolved(): void
    {
        $companyProduct = $this->createCompanyProduct();
        $formula = Formula::factory()->create();

        $this->createProductFormula(
            $companyProduct,
            $formula,
            null
        );

        $this->expectException(FormulaVersionNotFoundException::class);

        app(ProductFormulaResolver::class)->resolve(
            $companyProduct,
            app(FormulaVersionResolver::class)
        );
    }
}
