<?php

namespace App\Modules\Policies\Http\Resources;

use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Claim */
final class ClaimResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'claim_number' => $this->claim_number,
            'policy_id' => $this->policy_id,
            'status' => $this->status->value,
            'requested_amount' => $this->requested_amount,
            'description' => $this->description,
        ];
    }
}
