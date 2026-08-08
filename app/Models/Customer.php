<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends BaseTenantModel
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'mobile',
        'email',
        'national_code',
        'birth_date',
        'status',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'birth_date' => 'date',
    ];

    public function quotes(): HasMany
    {
        return $this->hasMany(
            Quote::class,
            'customer_id'
        );
    }

    public function policies(): HasMany
    {
        return $this->hasMany(
            Policy::class,
            'customer_id'
        );
    }
}
