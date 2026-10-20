<?php

namespace App\Modules\Policies\Http\Resources;

use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Policy */
final class PolicyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'policy_number' => $this->policy_number,
            'status' => $this->status,
            'premium' => $this->premium,
            'quote_id' => $this->quote_id,
            'quote_offer_id' => $this->quote_offer_id,
        ];
    }
}
