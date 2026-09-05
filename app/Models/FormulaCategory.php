<?php

namespace App\Models;

class FormulaCategory extends BaseTenantModel
{
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'is_active',
    ];
}
