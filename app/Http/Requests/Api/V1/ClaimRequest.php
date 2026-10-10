<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ClaimRequest extends FormRequest
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
        return [
            'policy_id' => [
                'required',
                'integer',
            ],
            'requested_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'description' => [
                'required',
                'string',
                'min:5',
            ],
        ];
    }
}
