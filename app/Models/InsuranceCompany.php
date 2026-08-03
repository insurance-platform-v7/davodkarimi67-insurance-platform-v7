<?php

// File: app/Models/InsuranceCompany.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
class InsuranceCompany extends BaseTenantModel
{
    use HasFactory , BelongsToTenant;


    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'active',
        'meta',
    ];

    protected $attributes = [
        'active' => true,
    ];

    protected $casts = [
        'meta' => 'array',
        'active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
