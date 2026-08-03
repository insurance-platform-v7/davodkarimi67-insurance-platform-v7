<?php

// File: app/Http/Requests/PaymentCallbackRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_id' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_id.required' => 'Transaction id is required.',
            'transaction_id.string' => 'Transaction id must be a string.',
        ];
    }
}
