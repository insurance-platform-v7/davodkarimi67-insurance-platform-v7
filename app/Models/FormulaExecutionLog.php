<?php

namespace App\Models;

class FormulaExecutionLog extends BaseTenantModel
{
    protected $fillable = [
        'tenant_id',
        'formula_id',
        'formula_version_id',
        'quote_id',
        'quote_offer_id',
        'input_data',
        'output_data',
        'duration_ms',
        'successful',
        'error_message',
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'successful' => 'boolean',
    ];
}
