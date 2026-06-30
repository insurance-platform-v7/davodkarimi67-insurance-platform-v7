<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ClaimDocument extends Model
{
    protected $fillable = [
        'claim_id',
        'type',
        'file_name',
        'path',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(
            Claim::class
        );
    }

    public function documents(): HasMany
    {
        return $this->hasMany(
            ClaimDocument::class
        );
    }

}
