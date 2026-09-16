<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\Customer;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Claim\ClaimDocumentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClaimDocumentServiceTest extends TestCase
{
    public function test_service_exists(): void
    {
        $service = app(ClaimDocumentService::class);

        $this->assertInstanceOf(
            ClaimDocumentService::class,
            $service
        );
    }

    public function test_upload_stores_file_and_creates_document(): void
    {
        Storage::fake('public');

        $tenant = Tenant::factory()->create();

        app()->instance('tenant', $tenant);

        $customer = Customer::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
        ]);

        $claim = Claim::factory()->create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
        ]);

        $file = UploadedFile::fake()->create(
            'accident.pdf',
            100,
            'application/pdf'
        );

        $document = app(ClaimDocumentService::class)->upload(
            $claim,
            $file,
            'evidence'
        );

        $this->assertInstanceOf(
            ClaimDocument::class,
            $document
        );

        $this->assertSame($claim->id, $document->claim_id);
        $this->assertSame('evidence', $document->type);
        $this->assertSame('accident.pdf', $document->file_name);
        $this->assertNotEmpty($document->path);

        Storage::disk('public')->assertExists($document->path);

        $this->assertDatabaseHas('claim_documents', [
            'id' => $document->id,
            'tenant_id' => $tenant->id,
            'claim_id' => $claim->id,
            'type' => 'evidence',
            'file_name' => 'accident.pdf',
            'path' => $document->path,
        ]);
    }
}
