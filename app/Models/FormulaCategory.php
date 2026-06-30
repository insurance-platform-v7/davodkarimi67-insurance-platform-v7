<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaCategory extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'is_active',
    ];
}
