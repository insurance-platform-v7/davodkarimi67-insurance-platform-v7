<?php

namespace Tests\Feature;

use App\Models\Formula;
use App\Models\FormulaVersion;
use App\Services\Formula\FormulaConditionLoader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FormulaConditionLoaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_load_returns_conditions_ordered_by_priority(): void
    {
        $formula = Formula::factory()->create();

        $version = FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
        ]);

        DB::table('formula_conditions')->insert([
            [
                'formula_version_id' => $version->id,
                'priority' => 20,
                'field' => 'age',
                'comparison' => '>=',
                'value' => 18,
                'group_type' => 'AND',
            ],
            [
                'formula_version_id' => $version->id,
                'priority' => 10,
                'field' => 'score',
                'comparison' => '>',
                'value' => 80,
                'group_type' => 'AND',
            ],
        ]);

        $result = app(FormulaConditionLoader::class)->load($version);

        $this->assertCount(2, $result);
        $this->assertSame('score', $result[0]['field']);
        $this->assertSame('age', $result[1]['field']);
        $this->assertSame('>', $result[0]['comparison']);
        $this->assertSame(18, $result[1]['value']);
    }

    public function test_load_returns_empty_array_when_no_conditions_exist(): void
    {
        $formula = Formula::factory()->create();

        $version = FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
        ]);

        $this->assertSame(
            [],
            app(FormulaConditionLoader::class)->load($version)
        );
    }
}
