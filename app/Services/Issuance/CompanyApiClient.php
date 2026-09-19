<?php

namespace App\Services\Issuance;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompanyApiClient
{
    protected int $timeout = 30;

    protected int $retryTimes = 3;

    protected int $retrySleep = 100;

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     * @return array<string, mixed>
     */
    public function send(
        string $url,
        array $payload = [],
        array $headers = []
    ): array {

        $this->log(
            'request',
            [
                'url' => $url,
                'payload' => $payload,
            ]
        );

        $response = Http::withHeaders($headers)
            ->timeout($this->timeout)
            ->retry(
                $this->retryTimes,
                $this->retrySleep
            )
            ->post(
                $url,
                $payload
            );

        $result = [
            'success' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json(),
        ];

        $this->log(
            'response',
            $result
        );

        return $result;
    }

    public function retry(
        int $times,
        int $sleepMilliseconds = 100
    ): static {

        $this->retryTimes = $times;
        $this->retrySleep = $sleepMilliseconds;

        return $this;
    }

    public function timeout(
        int $seconds
    ): static {

        $this->timeout = $seconds;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function log(
        string $type,
        array $context = []
    ): void {

        Log::info(
            'company_api_client.'.$type,
            $context
        );
    }
}
