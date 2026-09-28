<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property mixed $policyNumber
 * @property mixed $status
 * @property mixed $premium
 */
class PolicyResource extends JsonResource
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
        ];
    }
}

