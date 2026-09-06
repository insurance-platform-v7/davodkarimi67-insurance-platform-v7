<?php

namespace App\Http\Resources\Api\V1;

use App\Models\InsuranceCompany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin InsuranceCompany
 */
class InsuranceCompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->code,
            'is_active' => $this->active,
            'created_at' => $this->created_at,
        ];
    }
}
