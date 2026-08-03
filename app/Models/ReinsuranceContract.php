<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReinsuranceContract extends Model
{
    protected $fillable = [
        'name',
        'type',
        'retention_limit',
        'cession_rate',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'retention_limit' => 'decimal:2',
        'cession_rate' => 'decimal:2',
    ];
    public function allocations(): HasMany
    {
        return $this->hasMany(
            ReinsuranceAllocation::class
        );
    }

}
