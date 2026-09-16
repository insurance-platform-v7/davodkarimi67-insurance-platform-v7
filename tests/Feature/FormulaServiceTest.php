<?php

namespace Tests\Feature;

use App\Infrastructure\Formula\FormulaEngineAdapter;
use App\Models\CompanyProduct;
use App\Models\FormulaVersion;
use App\Models\ProductFormula;
use App\Services\Formula\DefaultFormulaCalculator;
use App\Services\Formula\FormulaConditionLoader;
use App\Services\Formula\FormulaExecutor;
use App\Services\Formula\FormulaService;
use App\Services\Formula\FormulaVersionResolver;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class FormulaServiceTest extends TestCase
{
    #[Test]
    public function formula_service_can_be_resolved(): void
    {
        $service = app(FormulaService::class);

        $this->assertInstanceOf(
            FormulaService::class,
            $service
        );
    }

    #[Test]
    public function it_uses_default_calculator_when_product_formula_is_missing(): void
    {
        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->with('version')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $defaultCalculator = Mockery::mock(
            DefaultFormulaCalculator::class
        );

        $defaultCalculator
            ->shouldReceive('calculate')
            ->once()
            ->with(['car_value' => 1000000])
            ->andReturn(30000);

        $service = new FormulaService(
            Mockery::mock(FormulaVersionResolver::class),
            Mockery::mock(FormulaConditionLoader::class),
            Mockery::mock(FormulaExecutor::class),
            $defaultCalculator,
            Mockery::mock(FormulaEngineAdapter::class),
        );

        $this->assertSame(
            30000,
            $service->calculateForProduct(
                $companyProduct,
                ['car_value' => 1000000]
            )
        );
    }

    #[Test]
    public function it_uses_default_calculator_when_product_formula_has_no_version(): void
    {
        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->with('version')
            ->once()
            ->andReturn($relation);

        $productFormula = new ProductFormula([
            'formula_id' => 123,
        ]);

        $productFormula->setRelation('version', null);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

        $versionResolver = Mockery::mock(
            FormulaVersionResolver::class
        );

        $versionResolver
            ->shouldReceive('resolve')
            ->once()
            ->with(123)
            ->andReturn(null);

        $defaultCalculator = Mockery::mock(
            DefaultFormulaCalculator::class
        );

        $defaultCalculator
            ->shouldReceive('calculate')
            ->once()
            ->with(['amount' => 500000])
            ->andReturn(15000);

        $service = new FormulaService(
            $versionResolver,
            Mockery::mock(FormulaConditionLoader::class),
            Mockery::mock(FormulaExecutor::class),
            $defaultCalculator,
            Mockery::mock(FormulaEngineAdapter::class),
        );

        $this->assertSame(
            15000,
            $service->calculateForProduct(
                $companyProduct,
                ['amount' => 500000]
            )
        );
    }

    #[Test]
    public function it_executes_legacy_formula_when_conditions_pass(): void
    {
        config()->set('features.formula_engine_v2', false);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->with('version')
            ->once()
            ->andReturn($relation);

        $version = new FormulaVersion([
            'formula_id' => 10,
            'formula_json' => [
                'type' => 'percentage',
                'value' => 3,
            ],
        ]);

        $productFormula = new ProductFormula([
            'formula_id' => 10,
        ]);

        $productFormula->setRelation('version', $version);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

        $conditions = [
            ['field' => 'car_value', 'operator' => '>', 'value' => 0],
        ];

        $conditionLoader = Mockery::mock(
            FormulaConditionLoader::class
        );

        $conditionLoader
            ->shouldReceive('load')
            ->once()
            ->with($version)
            ->andReturn($conditions);

        $executor = Mockery::mock(
            FormulaExecutor::class
        );

        $executor
            ->shouldReceive('canExecute')
            ->once()
            ->with(
                $conditions,
                ['car_value' => 1000000]
            )
            ->andReturn(true);

        $executor
            ->shouldReceive('execute')
            ->once()
            ->with(
                $version->formula_json,
                ['car_value' => 1000000]
            )
            ->andReturn(30000);

        $service = new FormulaService(
            Mockery::mock(FormulaVersionResolver::class),
            $conditionLoader,
            $executor,
            Mockery::mock(DefaultFormulaCalculator::class),
            Mockery::mock(FormulaEngineAdapter::class),
        );

        $this->assertSame(
            30000,
            $service->calculateForProduct(
                $companyProduct,
                ['car_value' => 1000000]
            )
        );
    }

    #[Test]
    public function it_throws_when_legacy_formula_conditions_fail(): void
    {
        config()->set('features.formula_engine_v2', false);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->with('version')
            ->once()
            ->andReturn($relation);

        $version = new FormulaVersion([
            'formula_id' => 10,
            'formula_json' => [
                'type' => 'percentage',
                'value' => 3,
            ],
        ]);

        $productFormula = new ProductFormula([
            'formula_id' => 10,
        ]);

        $productFormula->setRelation('version', $version);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

        $conditions = [
            ['field' => 'car_value', 'operator' => '>', 'value' => 10000000],
        ];

        $conditionLoader = Mockery::mock(
            FormulaConditionLoader::class
        );

        $conditionLoader
            ->shouldReceive('load')
            ->once()
            ->with($version)
            ->andReturn($conditions);

        $executor = Mockery::mock(
            FormulaExecutor::class
        );

        $executor
            ->shouldReceive('canExecute')
            ->once()
            ->with(
                $conditions,
                ['car_value' => 1000000]
            )
            ->andReturn(false);

        $executor
            ->shouldNotReceive('execute');

        $service = new FormulaService(
            Mockery::mock(FormulaVersionResolver::class),
            $conditionLoader,
            $executor,
            Mockery::mock(DefaultFormulaCalculator::class),
            Mockery::mock(FormulaEngineAdapter::class),
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Formula conditions failed.');

        $service->calculateForProduct(
            $companyProduct,
            ['car_value' => 1000000]
        );
    }

    #[Test]
    public function it_uses_default_calculator_when_v2_formula_is_empty(): void
    {
        config()->set('features.formula_engine_v2', true);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->with('version')
            ->once()
            ->andReturn($relation);

        $version = new FormulaVersion([
            'formula_id' => 10,
            'formula_json' => [],
        ]);

        $productFormula = new ProductFormula([
            'formula_id' => 10,
        ]);

        $productFormula->setRelation('version', $version);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

        $defaultCalculator = Mockery::mock(
            DefaultFormulaCalculator::class
        );

        $defaultCalculator
            ->shouldReceive('calculate')
            ->once()
            ->with(['car_value' => 1000000])
            ->andReturn(30000);

        $service = new FormulaService(
            Mockery::mock(FormulaVersionResolver::class),
            Mockery::mock(FormulaConditionLoader::class),
            Mockery::mock(FormulaExecutor::class),
            $defaultCalculator,
            Mockery::mock(FormulaEngineAdapter::class),
        );

        $this->assertSame(
            30000,
            $service->calculateForProduct(
                $companyProduct,
                ['car_value' => 1000000]
            )
        );
    }

    #[Test]
    public function it_uses_v2_adapter_when_feature_is_enabled(): void
    {
        config()->set('features.formula_engine_v2', true);

        $companyProduct = Mockery::mock(CompanyProduct::class);
        $relation = Mockery::mock(HasOne::class);

        $companyProduct
            ->shouldReceive('productFormula')
            ->once()
            ->andReturn($relation);

        $relation
            ->shouldReceive('with')
            ->with('version')
            ->once()
            ->andReturn($relation);

        $formula = [
            'type' => 'percentage',
            'value' => 3,
        ];

        $version = new FormulaVersion([
            'formula_id' => 10,
            'formula_json' => $formula,
        ]);

        $productFormula = new ProductFormula([
            'formula_id' => 10,
        ]);

        $productFormula->setRelation('version', $version);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($productFormula);

        $adapter = Mockery::mock(
            FormulaEngineAdapter::class
        );

        $adapter
            ->shouldReceive('calculate')
            ->once()
            ->with(
                $formula,
                ['car_value' => 1000000]
            )
            ->andReturn(30000);

        $service = new FormulaService(
            Mockery::mock(FormulaVersionResolver::class),
            Mockery::mock(FormulaConditionLoader::class),
            Mockery::mock(FormulaExecutor::class),
            Mockery::mock(DefaultFormulaCalculator::class),
            $adapter,
        );

        $this->assertSame(
            30000,
            $service->calculateForProduct(
                $companyProduct,
                ['car_value' => 1000000]
            )
        );
    }
}
