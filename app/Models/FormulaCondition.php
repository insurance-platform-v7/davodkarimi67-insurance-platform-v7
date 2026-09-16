<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormulaCondition extends BaseTenantModel
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
        'priority' => 'integer',
    ];

    /**
     * @return BelongsTo<FormulaVersion, $this>
     */
    public function formulaVersion(): BelongsTo
    {
        return $this->belongsTo(
            FormulaVersion::class
        );
    }

    /**
     * @return BelongsTo<FormulaCondition, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    /**
     * @return HasMany<FormulaCondition, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        );
    }
}
