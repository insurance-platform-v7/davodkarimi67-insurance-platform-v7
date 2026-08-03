<?php

namespace Tests\Feature;

use App\Models\InsuranceProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteCalculationDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_creation()
    {
        $product = InsuranceProduct::create([
            'name' => 'Car Insurance',
            'code' => 'car',
        ]);

        $this->assertDatabaseHas('insurance_products', [
            'code' => 'car',
        ]);
    }
}
