<?php

namespace App\Listeners;

use App\Events\QuoteOfferCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendQuoteOfferNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    public $backoff = [10, 30, 60];

    public function handle(QuoteOfferCreated $event): void
    {
        Log::info('Quote offer notification queued.', [
            'offer_id' => $event->offer->id,
            'quote_id' => $event->offer->quote_id,
            'company_id' => $event->offer->insurance_company_id,
        ]);
    }

    public function failed(QuoteOfferCreated $event, \Throwable $exception): void
    {
        Log::error('Quote offer notification failed.', [
            'offer_id' => $event->offer->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
