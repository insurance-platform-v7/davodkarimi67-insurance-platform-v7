<?php

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
            'transaction_id' => [
                'required',
                'string',
            ],

            'authority' => [
                'required',
                'string',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_id.required' =>
                'Transaction id is required.',

            'transaction_id.string' =>
                'Transaction id must be a string.',

            'authority.required' =>
                'Payment authority is required.',

            'authority.string' =>
                'Payment authority must be a string.',

            'amount.required' =>
                'Payment amount is required.',

            'amount.numeric' =>
                'Payment amount must be numeric.',

            'amount.min' =>
                'Payment amount must be greater than or equal to zero.',
        ];
    }
}
