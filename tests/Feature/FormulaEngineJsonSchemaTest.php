<?php

namespace Tests\Feature;

use App\Domain\Formula\FormulaEngine;
use App\Models\Formula;
use App\Models\FormulaVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
                'expression' => '1000000 * 0.02',
            ],
            'is_active' => true,
        ]);

        $engine = app(FormulaEngine::class);

        $result = $engine->execute(
            $formulaVersion->formula_json,
            []
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('premium', $result);
        $this->assertSame(20000.0, $result['premium']);
    }
}
