<?php

namespace Tests\Feature;

use App\Models\Formula;
use App\Models\FormulaVersion;
use App\Services\Formula\FormulaVersionResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormulaVersionResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolve_accepts_formula_model_and_returns_latest_active_version(): void
    {
        $formula = Formula::factory()->create();

        FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
            'version' => 1,
            'is_active' => true,
        ]);

        $latest = FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
            'version' => 2,
            'is_active' => true,
        ]);

        FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
            'version' => 3,
            'is_active' => false,
        ]);

        $result = app(FormulaVersionResolver::class)->resolve($formula);

        $this->assertNotNull($result);
        $this->assertSame($latest->id, $result->id);
        $this->assertSame('2', (string) $result->version);
    }

    public function test_resolve_accepts_formula_id(): void
    {
        $formula = Formula::factory()->create();

        $version = FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
            'version' => 5,
            'is_active' => true,
        ]);

        $result = app(FormulaVersionResolver::class)->resolve($formula->id);

        $this->assertNotNull($result);
        $this->assertSame($version->id, $result->id);
    }

    public function test_resolve_returns_null_when_no_active_version_exists(): void
    {
        $formula = Formula::factory()->create();

        FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
            'version' => 1,
            'is_active' => false,
        ]);

        $this->assertNull(
            app(FormulaVersionResolver::class)->resolve($formula)
        );
    }
}
