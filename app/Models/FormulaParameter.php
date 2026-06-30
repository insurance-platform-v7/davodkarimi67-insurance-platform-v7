<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaParameter extends Model
{
    protected $fillable = [
        'formula_id',
        'name',
        'key',
        'type',
        'is_required',
        'default_value',
        'validation_rules',
    ];

    protected $casts = [
        'default_value' => 'mixed',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
    ];
}
