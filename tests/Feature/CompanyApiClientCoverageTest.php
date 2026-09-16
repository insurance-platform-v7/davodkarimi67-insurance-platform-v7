<?php

namespace Tests\Feature;

use App\Services\Issuance\CompanyApiClient;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CompanyApiClientCoverageTest extends TestCase
{
    public function test_send_returns_successful_response_and_logs_request_and_response(): void
    {
        Http::fake([
            'https://example.test/*' => Http::response(
                ['ok' => true],
                200
            ),
        ]);

        Log::spy();

        $result = app(CompanyApiClient::class)->send(
            'https://example.test/issue',
            ['policy_id' => 10],
            ['X-Test' => 'yes']
        );

        $this->assertTrue($result['success']);
        $this->assertSame(200, $result['status']);
        $this->assertSame(['ok' => true], $result['body']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://example.test/issue'
                && $request['policy_id'] === 10
                && $request->header('X-Test')[0] === 'yes';
        });

        Log::shouldHaveReceived('info')->twice();
    }

    public function test_send_throws_for_failed_http_response(): void
    {
        Http::fake([
            'https://example.test/*' => Http::response(
                ['error' => 'failed'],
                422
            ),
        ]);

        $this->expectException(RequestException::class);

        app(CompanyApiClient::class)->send(
            'https://example.test/issue'
        );
    }
}