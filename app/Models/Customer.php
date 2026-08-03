<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'birth_date' => 'date',
    ];

    public function policies(): HasMany
    {
        return $this->hasMany(
            Policy::class
        );
    }

}
