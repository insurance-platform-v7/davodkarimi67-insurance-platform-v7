<?php

namespace Tests\Feature;

use App\Models\CompanyProduct;
use App\Models\ProductFormula;
use App\Models\FormulaVersion;
use App\Services\Formula\FormulaService;
use App\Services\Quote\PremiumCalculator;
use App\Domain\Formula\FormulaEngine;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class PremiumCalculatorTest extends TestCase
{
    public function test_it_calculates_premium_using_legacy_formula_service(): void
    {
        Config::set('features.formula_engine_v2', false);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $formulaService = Mockery::mock(FormulaService::class);
        $formulaEngine = Mockery::mock(FormulaEngine::class);

        $formulaService
            ->shouldReceive('calculateForProduct')
            ->once()
            ->with($companyProduct, ['car_value' => 50000])
            ->andReturn(1500.75);

        $calculator = new PremiumCalculator(
            $formulaService,
            $formulaEngine
        );

        $result = $calculator->calculateForInput(
            $companyProduct,
            ['car_value' => 50000]
        );

        $this->assertSame(1501, $result);
    }

    public function test_it_rejects_negative_legacy_premium(): void
    {
        Config::set('features.formula_engine_v2', false);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $formulaService = Mockery::mock(FormulaService::class);
        $formulaEngine = Mockery::mock(FormulaEngine::class);

        $formulaService
            ->shouldReceive('calculateForProduct')
            ->once()
            ->with($companyProduct, [])
            ->andReturn(-100);

        $calculator = new PremiumCalculator(
            $formulaService,
            $formulaEngine
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid premium calculated.');

        $calculator->calculateForInput($companyProduct, []);
    }
    public function test_v2_formula_engine_calculates_premium(): void
    {
        Config::set('features.formula_engine_v2', true);
        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);
        $productFormula = Mockery::mock(ProductFormula::class);
        $version = new FormulaVersion();
        $version->formula_json = ['premium' => 2750];
        $companyProduct->shouldReceive('productFormula')->once()->andReturn($relation);
        $relation->shouldReceive('with')->once()->with('version')->andReturn($relation);
        $relation->shouldReceive('first')->once()->andReturn($productFormula);
        $productFormula->shouldReceive('getRelation')->once()->with('version')->andReturn($version);
        $engine = Mockery::mock(FormulaEngine::class);
        $engine->shouldReceive('execute')->once()->with(['premium' => 2750], ['car_value' => 50000])->andReturn(['premium' => 2750]);
        $calculator = new PremiumCalculator(Mockery::mock(FormulaService::class), $engine);
        $this->assertSame(2750, $calculator->calculateForInput($companyProduct, ['car_value' => 50000]));
    }

    public function test_v2_rejects_when_product_formula_is_missing(): void
    {
        Config::set('features.formula_engine_v2', true);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);

        $companyProduct->shouldReceive('productFormula')->once()->andReturn($relation);
        $relation->shouldReceive('with')->once()->with('version')->andReturn($relation);
        $relation->shouldReceive('first')->once()->andReturn(null);

        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            Mockery::mock(FormulaEngine::class)
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No active product formula found.');

        $calculator->calculateForInput($companyProduct, []);
    }

    public function test_v2_rejects_when_product_formula_is_empty(): void
    {
        Config::set('features.formula_engine_v2', true);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);
        $productFormula = new ProductFormula();
        $productFormula->formula_json = [];
        $version = new FormulaVersion();
        $version->formula_json = [];

        $companyProduct->shouldReceive('productFormula')->once()->andReturn($relation);
        $relation->shouldReceive('with')->once()->with('version')->andReturn($relation);
        $relation->shouldReceive('first')->once()->andReturn($productFormula);

        $productFormula->setRelation('version', $version);

        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            Mockery::mock(FormulaEngine::class)
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Product formula is empty.');

        $calculator->calculateForInput($companyProduct, []);
    }

    public function test_v2_rejects_invalid_engine_premium(): void
    {
        Config::set('features.formula_engine_v2', true);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);
        $productFormula = new ProductFormula();
        $version = new FormulaVersion();
        $version->formula_json = ['premium' => 2750];

        $companyProduct->shouldReceive('productFormula')->once()->andReturn($relation);
        $relation->shouldReceive('with')->once()->with('version')->andReturn($relation);
        $relation->shouldReceive('first')->once()->andReturn($productFormula);

        $productFormula->setRelation('version', $version);

        $engine = Mockery::mock(FormulaEngine::class);
        $engine->shouldReceive('execute')
            ->once()
            ->with(['premium' => 2750], [])
            ->andReturn(['premium' => 'invalid']);

        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            $engine
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Formula engine returned an invalid premium.');

        $calculator->calculateForInput($companyProduct, []);
    }

    public function test_v2_rejects_negative_engine_premium(): void
    {
        Config::set('features.formula_engine_v2', true);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);
        $productFormula = new ProductFormula();
        $version = new FormulaVersion();
        $version->formula_json = ['premium' => -100];

        $companyProduct->shouldReceive('productFormula')->once()->andReturn($relation);
        $relation->shouldReceive('with')->once()->with('version')->andReturn($relation);
        $relation->shouldReceive('first')->once()->andReturn($productFormula);

        $productFormula->setRelation('version', $version);

        $engine = Mockery::mock(FormulaEngine::class);
        $engine->shouldReceive('execute')
            ->once()
            ->with(['premium' => -100], [])
            ->andReturn(['premium' => -100]);

        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            $engine
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid premium calculated.');

        $calculator->calculateForInput($companyProduct, []);
    }
}
