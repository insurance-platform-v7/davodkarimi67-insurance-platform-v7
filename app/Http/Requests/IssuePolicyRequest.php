<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssuePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'offer_id' => [
                'required',
                'integer',
                'exists:quote_offers,id',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'offer_id.required' => 'Offer id is required.',
            'offer_id.integer' => 'Offer id must be an integer.',
            'offer_id.exists' => 'Selected offer does not exist.',
        ];
    }
}
