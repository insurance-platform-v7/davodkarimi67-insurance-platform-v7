<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'insurance_product_id',
        'quote_number',
        'input_data',
        'status',
    ];

    protected $casts = [
        'input_data' => 'array',
    ];
}
