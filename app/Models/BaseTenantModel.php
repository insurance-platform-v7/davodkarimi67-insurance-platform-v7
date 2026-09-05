<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class BaseTenantModel extends Model
{
    use BelongsToTenant;
}
