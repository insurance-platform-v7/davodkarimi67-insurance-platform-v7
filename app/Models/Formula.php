<?php

namespace App\Models;

use Database\Factories\FormulaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formula extends BaseTenantModel
{
    /** @use HasFactory<FormulaFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'formula_category_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** @return HasMany<FormulaVersion, $this> */
    public function versions(): HasMany
    {
        return $this->hasMany(
            FormulaVersion::class,
            'formula_id'
        );
    }
}
