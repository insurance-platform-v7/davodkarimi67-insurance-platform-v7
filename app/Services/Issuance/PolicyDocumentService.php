<?php

namespace App\Services\Issuance;

use App\Models\Document;
use App\Models\Policy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PolicyDocumentService
{
    public function __construct(
        protected PdfTemplateResolver $templateResolver
    ) {
    }

    public function generate(Policy $policy): Document
    {
        $template = $this->templateResolver
            ->resolve($policy);

        $pdf = Pdf::loadView($template, [
            'policy' => $policy,
        ]);

        $fileName = $policy->policy_number . '.pdf';

        $path = 'policies/' . $fileName;

        Storage::disk('public')->put(
            $path,
            $pdf->output()
        );

        return Document::create([
            'policy_id' => $policy->id,
            'type' => 'policy_pdf',
            'path' => $path,
            'meta' => [
                'template' => $template,
                'generated_at' => now(),
            ],
        ]);
    }
}
