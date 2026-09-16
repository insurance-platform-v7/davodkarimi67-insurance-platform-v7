<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends BaseTenantModel
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

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

    /** @return HasMany<Quote, $this> */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'customer_id');
    }

    /** @return HasMany<Policy, $this> */
    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class, 'customer_id');
    }
}
