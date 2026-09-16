<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteRequest extends Model
{
    // ...

    /**
     * @return HasMany<QuoteOption, $this>
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuoteOption::class);
    }

    /**
     * @return BelongsTo<InsuranceProduct, $this>
     */
    public function insuranceType(): BelongsTo
    {
        return $this->belongsTo(InsuranceProduct::class);
    }
}
