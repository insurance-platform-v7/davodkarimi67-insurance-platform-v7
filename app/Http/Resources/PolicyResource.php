<?php
// File: app/Http/Resources/PolicyResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'policy_number' => $this->policy_number,
            'status' => $this->status?->value ?? $this->status,
            'premium' => $this->premium,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
        ];
    }
}
