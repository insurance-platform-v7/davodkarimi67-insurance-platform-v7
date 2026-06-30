<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductFormula extends Model
{
    use HasFactory;

    protected $fillable = [
        'formula_id',
        'insurance_product_id',
        'name',
        'version',
        'is_active',
    ];
}
