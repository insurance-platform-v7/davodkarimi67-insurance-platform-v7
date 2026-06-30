<?php

namespace Tests\Feature;

use App\Services\Issuance\CompanyApiClient;
use Tests\TestCase;

class CompanyApiClientTest extends TestCase
{
    public function test_client_configuration()
    {
        $client = app(
            CompanyApiClient::class
        );

        $result = $client
            ->timeout(10)
            ->retry(5);

        $this->assertInstanceOf(
            CompanyApiClient::class,
            $result
        );
    }
}
