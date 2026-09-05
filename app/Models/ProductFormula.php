<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFormula extends BaseTenantModel
{
    use BelongsToTenant, HasFactory;

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

    public function formula(): BelongsTo
    {
        return $this->belongsTo(
            Formula::class,
            'formula_id'
        );
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(
            FormulaVersion::class,
            'formula_version_id'
        );
    }
}
