<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Formula;
use App\Models\FormulaVersion;
use App\Services\Formula\FormulaExecutor;
use PHPUnit\Framework\Attributes\Test;

class FormulaEngineJsonSchemaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_calculates_premium_using_json_schema(): void
    {
        $formula = Formula::create([
            'name' => 'Premium Formula',
            'code' => 'premium-formula',
            'is_active' => true,
        ]);

        $formulaVersion = FormulaVersion::create([
            'formula_id' => $formula->id,
            'version' => '1.0',
            'formula_json' => [
                'type' => 'expression',
                'expression' => '1000000 * 0.02'
            ],
            'is_active' => true,
        ]);

        $executor = app(FormulaExecutor::class);

        $result = $executor->execute(
            $formulaVersion->formula_json,
            []
        );

        $this->assertEquals(20000, $result);
    }
}
