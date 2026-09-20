<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteCalculateRequest extends FormRequest
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
            'product_id' => 'required|integer',
            'vehicle_value' => 'required|numeric',
            'driver_age' => 'required|integer|min:18|max:99',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'The product ID field is required.',
            'product_id.integer' => 'The product ID must be an integer.',
            'vehicle_value.required' => 'The vehicle value field is required.',
            'vehicle_value.numeric' => 'The vehicle value must be a number.',
            'driver_age.required' => 'The driver age field is required.',
            'driver_age.integer' => 'The driver age must be an integer.',
            'driver_age.min' => 'The driver age must be at least 18.',
            'driver_age.max' => 'The driver age cannot be greater than 99.',
        ];
    }
}
