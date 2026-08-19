<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class FormulaCategory extends BaseTenantModel
{

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'is_active',
    ];
}

