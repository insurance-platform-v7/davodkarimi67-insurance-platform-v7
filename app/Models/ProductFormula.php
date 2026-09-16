<?php

namespace App\Models;

use Database\Factories\ProductFormulaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFormula extends BaseTenantModel
{
    /** @use HasFactory<ProductFormulaFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'insurance_product_id',
        'insurance_company_id',
        'formula_id',
        'formula_version_id',
        'name',
        'version',
        'formula_json',
        'is_active',
    ];

    protected $casts = [
        'formula_json' => 'array',
        'is_active' => 'boolean',
    ];

    /** @return BelongsTo<Formula, $this> */
    public function formula(): BelongsTo
    {
        return $this->belongsTo(
            Formula::class,
            'formula_id'
        );
    }

    /** @return BelongsTo<FormulaVersion, $this> */
    public function version(): BelongsTo
    {
        return $this->belongsTo(
            FormulaVersion::class,
            'formula_version_id'
        );
    }
}
