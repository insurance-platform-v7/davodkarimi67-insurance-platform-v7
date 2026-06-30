<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormulaCondition extends Model
{
    protected $fillable = [
        'formula_version_id',
        'parent_id',
        'operator',
        'field',
        'comparison',
        'value',
        'group_type',
        'priority',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function formulaVersion(): BelongsTo
    {
        return $this->belongsTo(
            FormulaVersion::class
        );
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        );
    }
}
