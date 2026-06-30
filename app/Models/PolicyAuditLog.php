<?php
// File: app/Models/PolicyAuditLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PolicyAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
