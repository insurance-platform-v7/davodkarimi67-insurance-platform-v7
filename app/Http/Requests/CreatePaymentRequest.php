<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
                'exists:policies,id',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'policy_id.required' => 'Policy id is required.',
            'policy_id.integer' => 'Policy id must be an integer.',
            'policy_id.exists' => 'Selected policy does not exist.',
        ];
    }
}
