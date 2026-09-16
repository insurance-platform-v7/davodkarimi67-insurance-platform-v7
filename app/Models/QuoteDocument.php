<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteDocument extends BaseTenantModel
{
    protected $fillable = [
        'type',
        'path',
        'quote_request_id',
        'tenant_id',
    ];

    /** @return BelongsTo<QuoteRequest, $this> */
    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }
}
