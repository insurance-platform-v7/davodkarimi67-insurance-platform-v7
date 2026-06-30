<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'tenant_id',
        'documentable_type',
        'documentable_id',
        'uploaded_by',
        'type',
        'title',
        'disk',
        'path',
        'mime_type',
        'size',
        'status',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }
}
