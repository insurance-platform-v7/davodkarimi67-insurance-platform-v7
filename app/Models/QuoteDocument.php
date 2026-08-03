<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class QuoteDocument extends Model
{
    protected $fillable = [

        'type',
        'path',
        'quote_request_id',
    ];

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(
            QuoteRequest::class
        );
    }
}
