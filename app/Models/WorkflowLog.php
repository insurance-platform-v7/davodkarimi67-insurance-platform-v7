<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'entity_type',
        'entity_id',
        'from_state_id',
        'to_state_id',
        'user_id',
        'action',
        'payload',
        'note',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
