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
        return DB::transaction(function () use ($offerId) {
            $offer = QuoteOffer::query()
                ->lockForUpdate()
                ->findOrFail($offerId);

            $existingPolicy = Policy::query()
                ->where('quote_offer_id', $offer->id)
                ->first();

            if ($existingPolicy) {
                return $existingPolicy;
            }

            $policyNumber = $this->generateUniquePolicyNumber();

            return Policy::create([
                'tenant_id' => $offer->tenant_id,
                'quote_id' => $offer->quote_id,
                'quote_offer_id' => $offer->id,
                'customer_id' => null,
                'policy_number' => $policyNumber,
                'status' => PolicyStatus::ISSUED,
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
        for ($i = 0; $i < 10; $i++) {
            $number = 'P-' . strtoupper(Str::random(12));

            $exists = Policy::query()
                ->where('policy_number', $number)
                ->exists();

            if (! $exists) {
                return $number;
            }
        }

        throw new RuntimeException('Unable to generate unique policy number.');
    }
}
