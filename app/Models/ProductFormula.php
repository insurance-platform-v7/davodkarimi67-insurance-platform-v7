<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\BelongsToTenant;
class ProductFormula extends BaseTenantModel
{
    use HasFactory, BelongsToTenant;



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

    public function formula()
    {
        return $this->belongsTo(
            Formula::class
        );
    }

    public function version()
    {
        return $this->belongsTo(
            FormulaVersion::class,
            'formula_version_id'
        );
    }
}
