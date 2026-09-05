<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormulaVersion extends Model
{
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

    public function conditions()
    {
        return $this->hasMany(
            FormulaCondition::class,
            'formula_version_id'
        );
    }

    public function formula(): BelongsTo
    {
        return $this->belongsTo(
            Formula::class,
            'formula_id'
        );
    }
}
