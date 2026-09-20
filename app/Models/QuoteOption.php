<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteOption extends Model
{
    // ...

    /**
     * @return BelongsTo<QuoteRequest, $this>
     */
    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    /**
     * @return BelongsTo<InsuranceCompany, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }
}
