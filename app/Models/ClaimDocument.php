<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimDocument extends BaseTenantModel
{
    protected $fillable = [
        'claim_id',
        'type',
        'file_name',
        'path',
        'meta',
        'tenant_id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /** @return BelongsTo<Claim, $this> */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }
}
