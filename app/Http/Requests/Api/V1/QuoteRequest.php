<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'insurance_type_id' => 'required|exists:insurance_types,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'production_year' => 'required|integer|min:1380|max:1405',
            'usage_type' => 'nullable|string',
            // سایر فیلدهایی که در DTO استفاده کردیم
        ];
    }

    public function messages(): array
    {
        return [
            'insurance_type_id.required' => 'نوع بیمه الزامی است.',
            'production_year.min' => 'سال تولید نمی‌تواند کمتر از ۱۳۸۰ باشد.',
            // پیام‌های فارسی دلخواه
        ];
    }
}
