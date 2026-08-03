<?php

// File: app/Http/Requests/CreatePaymentRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'policy_id' => ['required', 'integer', 'exists:policies,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'policy_id.required' => 'Policy id is required.',
            'policy_id.integer' => 'Policy id must be an integer.',
            'policy_id.exists' => 'Selected policy does not exist.',
        ];
    }
}
