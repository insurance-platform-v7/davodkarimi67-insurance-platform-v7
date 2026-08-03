<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

abstract class BaseTenantModel extends Model
{
    use BelongsToTenant;
}
