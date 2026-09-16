<?php

namespace App\Services\Issuance;

use App\Models\Policy;

class PdfTemplateResolver
{
    public function resolve(Policy $policy): string
    {
        $companyValue = $policy->company_code
            ?? $policy->company
            ?? 'default';

        $company = is_string($companyValue)
            ? strtolower($companyValue)
            : 'default';

        return match ($company) {
            'asia' => 'documents.providers.asia',
            'dana' => 'documents.providers.dana',
            'mellat' => 'documents.providers.mellat',
            default => 'documents.policy',
        };
    }
}
