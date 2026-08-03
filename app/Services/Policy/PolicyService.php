<?php

namespace App\Services\Policy;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Models\QuoteOffer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PolicyService
{
    public function issueFromOffer(int $offerId): Policy
    {
        return DB::transaction(function () use ($offerId): Policy {

            $offer = QuoteOffer::query()
                ->lockForUpdate()
                ->findOrFail($offerId);

            if ($policy = Policy::query()
                ->where('quote_offer_id', $offer->id)
                ->first()) {
                return $policy;
            }

            return Policy::create([
                'tenant_id' => $offer->tenant_id,
                'quote_id' => $offer->quote_id,
                'quote_offer_id' => $offer->id,
                'customer_id' => null,
                'policy_number' => $this->generateUniquePolicyNumber(),
                'status' => PolicyStatus::QUOTE_CREATED,
                'premium' => $offer->premium,
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'meta' => [
                    'issued_from_offer' => $offer->id,
                ],
            ]);
        });
    }

    private function generateUniquePolicyNumber(): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {

            $policyNumber = 'P-'.strtoupper(Str::random(12));

            if (! Policy::query()
                ->where('policy_number', $policyNumber)
                ->exists()) {
                return $policyNumber;
            }
        }

        throw new RuntimeException(
            'Unable to generate unique policy number.'
        );
    }
}
