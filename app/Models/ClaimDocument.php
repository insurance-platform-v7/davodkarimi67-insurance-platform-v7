<?php

namespace App\Models;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function claim(): BelongsTo
    {
        return $this->belongsTo(
            Claim::class
        );
    }



}
