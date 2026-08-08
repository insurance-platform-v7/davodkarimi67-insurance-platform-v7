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
                'tenant_id'       => $offer->tenant_id,
                'quote_id'        => $offer->quote_id,
                'quote_offer_id'  => $offer->id,
                'customer_id'     => null,
                'policy_number'   => $this->generateUniquePolicyNumber(),
                'status'          => PolicyStatus::QUOTE_CREATED,
                'premium'         => $offer->premium,
                'starts_at'       => now(),
                'ends_at'         => now()->addYear(),
                'meta' => [
                    'issued_from_offer' => $offer->id,
                ],
            ]);
        });
    }

    protected function generateUniquePolicyNumber(): string
    {
        do {
            $policyNumber = 'POL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
        } while (
            Policy::where('policy_number', $policyNumber)->exists()
        );

        return $policyNumber;
    }

    protected function handleExpression(
        array $rule,
        Context $context
    ): void {

        $expression = $rule['expression'] ?? '';

        if ($expression === '') {
            throw new RuntimeException(
                'Formula expression is empty.'
            );
        }

        $expression = $this->variableResolver->replace(
            $expression,
            $context->input()
        );

        $result = $this->expressionResolver->evaluate(
            $expression
        );

        $context->set(
            'premium',
            $result
        );
    }
}
