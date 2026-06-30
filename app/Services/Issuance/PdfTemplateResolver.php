<?php

namespace App\Services\Issuance;

use App\Models\Policy;

class PdfTemplateResolver
{
    public function resolve(Policy $policy): string
    {
        $company = strtolower(
            $policy->company_code
            ?? $policy->company
            ?? 'default'
        );

        return match ($company) {

            'asia' => 'documents.providers.asia',

            'dana' => 'documents.providers.dana',

            'mellat' => 'documents.providers.mellat',

            default => 'documents.policy',
        };
    }
}
