<?php

namespace App\Models;

use Database\Factories\FormulaVersionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormulaVersion extends Model
{
    /** @use HasFactory<FormulaVersionFactory> */
    use HasFactory;

    protected $fillable = [
        'formula_id',
        'version',
        'formula_json',
        'is_active',
        'activated_at',
    ];

    protected $casts = [
        'formula_json' => 'array',
        'activated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * @return HasMany<FormulaCondition, $this>
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(
            FormulaCondition::class,
            'formula_version_id'
        );
    }

    /**
     * @return BelongsTo<Formula, $this>
     */
    public function formula(): BelongsTo
    {
        return $this->belongsTo(
            Formula::class,
            'formula_id'
        );
    }
}
