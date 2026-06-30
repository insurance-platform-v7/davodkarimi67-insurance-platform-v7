<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Formula;
use App\Models\FormulaVersion;

class FormulaVersioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_formula_version_creation()
    {
        $formula = Formula::create([
            'name'=>'Base Price Formula',
            'code'=>'base_price'
        ]);

        $version = FormulaVersion::create([
            'formula_id'=>$formula->id,
            'version'=>1,
            'formula_json'=>[
                'base_price'=>1000000
            ],
            'is_active'=>true
        ]);

        $this->assertDatabaseHas('formula_versions',[
            'formula_id'=>$formula->id
        ]);
    }
}
