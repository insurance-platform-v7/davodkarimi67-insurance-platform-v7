<?php

namespace Tests\Feature;

use App\Domain\Formula\FormulaEngine;
use App\Models\CompanyProduct;
use App\Models\FormulaVersion;
use App\Models\ProductFormula;
use App\Models\Quote;
use App\Services\Formula\FormulaService;
use App\Services\Quote\PremiumCalculator;
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

        $this->assertSame(
            1501,
            $calculator->calculateForInput(
                $companyProduct,
                ['car_value' => 50000]
            )
        );
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

        $version = new FormulaVersion;
        $version->formula_json = ['premium' => 2750];

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->once()
            ->with('version')
            ->andReturn($relation);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

        $productFormula
            ->shouldReceive('getRelation')
            ->once()
            ->with('version')
            ->andReturn($version);

        $engine = Mockery::mock(FormulaEngine::class);

        $engine
            ->shouldReceive('execute')
            ->once()
            ->with(
                ['premium' => 2750],
                ['car_value' => 50000]
            )
            ->andReturn(['premium' => 2750]);

        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            $engine
        );

        $this->assertSame(
            2750,
            $calculator->calculateForInput(
                $companyProduct,
                ['car_value' => 50000]
            )
        );
    }

    public function test_calculate_extracts_parameters_from_quote_input_data(): void
    {
        Config::set('features.formula_engine_v2', false);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $formulaService = Mockery::mock(FormulaService::class);

        $formulaService
            ->shouldReceive('calculateForProduct')
            ->once()
            ->with($companyProduct, ['car_value' => 70000])
            ->andReturn(2000);

        $quote = new Quote;
        $quote->input_data = ['car_value' => 70000];

        $calculator = new PremiumCalculator(
            $formulaService,
            Mockery::mock(FormulaEngine::class)
        );

        $this->assertSame(
            2000,
            $calculator->calculate($quote, $companyProduct)
        );
    }

    public function test_calculate_extracts_parameters_from_parameters_key(): void
    {
        Config::set('features.formula_engine_v2', false);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $formulaService = Mockery::mock(FormulaService::class);

        $formulaService
            ->shouldReceive('calculateForProduct')
            ->once()
            ->with($companyProduct, ['car_value' => 80000])
            ->andReturn(2100);

        $quote = new Quote;
        $quote->input_data = [];
        $quote->parameters = ['car_value' => 80000];

        $calculator = new PremiumCalculator(
            $formulaService,
            Mockery::mock(FormulaEngine::class)
        );

        $this->assertSame(
            2100,
            $calculator->calculate($quote, $companyProduct)
        );
    }

    public function test_calculate_extracts_parameters_from_meta_key(): void
    {
        Config::set('features.formula_engine_v2', false);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $formulaService = Mockery::mock(FormulaService::class);

        $formulaService
            ->shouldReceive('calculateForProduct')
            ->once()
            ->with($companyProduct, ['car_value' => 90000])
            ->andReturn(2200);

        $quote = new Quote;
        $quote->input_data = [];
        $quote->parameters = [];
        $quote->meta = ['car_value' => 90000];

        $calculator = new PremiumCalculator(
            $formulaService,
            Mockery::mock(FormulaEngine::class)
        );

        $this->assertSame(
            2200,
            $calculator->calculate($quote, $companyProduct)
        );
    }

    public function test_calculate_falls_back_to_json_attributes(): void
    {
        Config::set('features.formula_engine_v2', false);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $formulaService = Mockery::mock(FormulaService::class);

        $formulaService
            ->shouldReceive('calculateForProduct')
            ->once()
            ->with($companyProduct, ['car_value' => 100000])
            ->andReturn(2300);

        $quote = new Quote;

        $quote->setRawAttributes([
            'input_data' => json_encode(['car_value' => 100000]),
            'parameters' => null,
            'meta' => null,
        ]);

        $calculator = new PremiumCalculator(
            $formulaService,
            Mockery::mock(FormulaEngine::class)
        );

        $this->assertSame(
            2300,
            $calculator->calculate($quote, $companyProduct)
        );
    }

    public function test_v2_uses_product_formula_when_version_formula_is_unavailable(): void
    {
        Config::set('features.formula_engine_v2', true);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);
        $productFormula = new ProductFormula;

        $productFormula->formula_json = ['premium' => 3100];

        $version = new FormulaVersion;
        $version->formula_json = [];

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->once()
            ->with('version')
            ->andReturn($relation);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

        $productFormula->setRelation('version', $version);

        $engine = Mockery::mock(FormulaEngine::class);

        $engine
            ->shouldReceive('execute')
            ->once()
            ->with(
                ['premium' => 3100],
                []
            )
            ->andReturn(['premium' => 3100]);

        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            $engine
        );

        $this->assertSame(
            3100,
            $calculator->calculateForInput($companyProduct, [])
        );
    }

    public function test_v2_accepts_zero_premium(): void
    {
        Config::set('features.formula_engine_v2', true);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);
        $productFormula = new ProductFormula;

        $version = new FormulaVersion;
        $version->formula_json = ['premium' => 0];

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->once()
            ->with('version')
            ->andReturn($relation);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

        $productFormula->setRelation('version', $version);

        $engine = Mockery::mock(FormulaEngine::class);

        $engine
            ->shouldReceive('execute')
            ->once()
            ->with(
                ['premium' => 0],
                []
            )
            ->andReturn(['premium' => 0]);

        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            $engine
        );

        $this->assertSame(
            0,
            $calculator->calculateForInput($companyProduct, [])
        );
    }

    public function test_v2_rejects_when_product_formula_is_missing(): void
    {
        Config::set('features.formula_engine_v2', true);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->once()
            ->with('version')
            ->andReturn($relation);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn(null);

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
        $productFormula = new ProductFormula;

        $productFormula->formula_json = [];

        $version = new FormulaVersion;
        $version->formula_json = [];

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->once()
            ->with('version')
            ->andReturn($relation);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

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
        $productFormula = new ProductFormula;

        $version = new FormulaVersion;
        $version->formula_json = ['premium' => 2750];

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->once()
            ->with('version')
            ->andReturn($relation);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

        $productFormula->setRelation('version', $version);

        $engine = Mockery::mock(FormulaEngine::class);

        $engine
            ->shouldReceive('execute')
            ->once()
            ->with(
                ['premium' => 2750],
                []
            )
            ->andReturn(['premium' => 'invalid']);

        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            $engine
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Formula engine returned an invalid premium.'
        );

        $calculator->calculateForInput($companyProduct, []);
    }

    public function test_v2_rejects_negative_engine_premium(): void
    {
        Config::set('features.formula_engine_v2', true);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);
        $productFormula = new ProductFormula;

        $version = new FormulaVersion;
        $version->formula_json = ['premium' => -100];

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->once()
            ->with('version')
            ->andReturn($relation);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

        $productFormula->setRelation('version', $version);

        $engine = Mockery::mock(FormulaEngine::class);

        $engine
            ->shouldReceive('execute')
            ->once()
            ->with(
                ['premium' => -100],
                []
            )
            ->andReturn(['premium' => -100]);

        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            $engine
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid premium calculated.');

        $calculator->calculateForInput($companyProduct, []);
    }

    public function test_calculate_uses_empty_parameters_when_all_quote_sources_are_empty(): void
    {
        Config::set('features.formula_engine_v2', false);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $formulaService = Mockery::mock(FormulaService::class);

        $formulaService
            ->shouldReceive('calculateForProduct')
            ->once()
            ->with($companyProduct, [])
            ->andReturn(1200);

        $quote = new Quote;

        $calculator = new PremiumCalculator(
            $formulaService,
            Mockery::mock(FormulaEngine::class)
        );

        $this->assertSame(
            1200,
            $calculator->calculate($quote, $companyProduct)
        );
    }

    public function test_normalize_source_ignores_non_array_source(): void
    {
        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            Mockery::mock(FormulaEngine::class)
        );

        $reflection = new \ReflectionClass($calculator);
        $method = $reflection->getMethod('normalizeSource');

        $this->assertSame(
            [],
            $method->invoke($calculator, 'invalid')
        );
    }

    public function test_normalize_attribute_returns_empty_for_empty_string(): void
    {
        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            Mockery::mock(FormulaEngine::class)
        );

        $reflection = new \ReflectionClass($calculator);
        $method = $reflection->getMethod('normalizeAttribute');

        $this->assertSame(
            [],
            $method->invoke($calculator, '   ')
        );
    }

    public function test_normalize_attribute_returns_empty_for_invalid_json(): void
    {
        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            Mockery::mock(FormulaEngine::class)
        );

        $reflection = new \ReflectionClass($calculator);
        $method = $reflection->getMethod('normalizeAttribute');

        $this->assertSame(
            [],
            $method->invoke($calculator, '{invalid-json')
        );
    }

    public function test_normalize_parameters_uses_input_data_shape(): void
    {
        $calculator = new PremiumCalculator(
            Mockery::mock(FormulaService::class),
            Mockery::mock(FormulaEngine::class)
        );

        $reflection = new \ReflectionClass($calculator);
        $method = $reflection->getMethod('normalizeParameters');

        $this->assertSame(
            ['car_value' => 125000],
            $method->invoke(
                $calculator,
                [
                    'input_data' => [
                        'car_value' => 125000,
                    ],
                ]
            )
        );
    }
}
