<?php

namespace App\Services\Claim;

use App\Models\Claim;
use App\Models\ClaimDocument;
use Illuminate\Http\UploadedFile;

class ClaimDocumentService
{
    public function upload(
        Claim $claim,
        UploadedFile $file,
        string $type = 'attachment'
    ): ClaimDocument {

        $path = $file->store(
            'claims',
            'public'
        );

        return ClaimDocument::create([
            'claim_id' => $claim->id,
            'type' => $type,
            'file_name' => $file->getClientOriginalName(),
            'path' => $path,
            'meta' => [],
        ]);
    }
}
