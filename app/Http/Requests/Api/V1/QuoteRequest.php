<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Tenant;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return app()->bound('tenant');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $tenant = app('tenant');

        if (! $tenant instanceof Tenant) {
            abort(500, 'Tenant context is not available.');
        }

        $tenantId = $tenant->id;

        return [
            'insurance_product_id' => [
                'required',
                'integer',
                Rule::exists('insurance_products', 'id')
                    ->where(function (Builder $query) use ($tenantId): void {
                        $query->where(function (Builder $query) use ($tenantId): void {
                            $query->whereNull('tenant_id')
                                ->orWhere('tenant_id', $tenantId);
                        });
                    }),
            ],
            'customer_id' => [
                'required',
                'integer',
                Rule::exists('customers', 'id')
                    ->where(fn (Builder $query) => $query->where('tenant_id', $tenantId)),
            ],
            'parameters' => [
                'nullable',
                'array',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'insurance_product_id.required' => 'The insurance product ID field is required.',
            'insurance_product_id.integer' => 'The insurance product ID must be an integer.',
            'insurance_product_id.exists' => 'The selected insurance product is invalid for this tenant.',
            'customer_id.required' => 'The customer ID field is required.',
            'customer_id.integer' => 'The customer ID must be an integer.',
            'customer_id.exists' => 'The selected customer is invalid for this tenant.',
            'parameters.array' => 'The parameters field must be an array.',
        ];
    }
}
