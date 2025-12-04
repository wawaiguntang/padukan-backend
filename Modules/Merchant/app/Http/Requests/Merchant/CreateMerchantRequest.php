<?php

namespace Modules\Merchant\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Merchant\Enums\BusinessCategoryEnum;

/**
 * Create Merchant Request
 *
 * Validates data for creating a new merchant
 */
class CreateMerchantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'business_description' => ['nullable', 'string', 'max:1000'],
            'business_category' => ['required', 'string', 'in:' . implode(',', array_column(BusinessCategoryEnum::cases(), 'value'))],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'logo_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png', 'max:2048'],
            'street' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:10'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180']
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'business_name.required' => __('merchant::validation.business_name.required'),
            'business_name.string' => __('merchant::validation.business_name.string'),
            'business_name.max' => __('merchant::validation.business_name.max'),
            'business_description.string' => __('merchant::validation.business_description.string'),
            'business_description.max' => __('merchant::validation.business_description.max'),
            'business_category.required' => __('merchant::validation.business_category.required'),
            'business_category.in' => __('merchant::validation.business_category.in'),
            'phone.required' => __('merchant::validation.phone.required'),
            'phone.string' => __('merchant::validation.phone.string'),
            'phone.max' => __('merchant::validation.phone.max'),
            'email.email' => __('merchant::validation.email.email'),
            'email.max' => __('merchant::validation.email.max'),
            'website.url' => __('merchant::validation.website.url'),
            'website.max' => __('merchant::validation.website.max'),
            'logo_file.file' => __('merchant::validation.merchant_verification.logo_file.file'),
            'logo_file.mimes' => __('merchant::validation.merchant_verification.logo_file.mimes'),
            'logo_file.max' => __('merchant::validation.merchant_verification.logo_file.max'),
            'street.required' => __('merchant::validation.street.required'),
            'street.string' => __('merchant::validation.street.string'),
            'street.max' => __('merchant::validation.street.max'),
            'city.required' => __('merchant::validation.city.required'),
            'city.string' => __('merchant::validation.city.string'),
            'city.max' => __('merchant::validation.city.max'),
            'province.required' => __('merchant::validation.province.required'),
            'province.string' => __('merchant::validation.province.string'),
            'province.max' => __('merchant::validation.province.max'),
            'country.required' => __('merchant::validation.country.required'),
            'country.string' => __('merchant::validation.country.string'),
            'country.max' => __('merchant::validation.country.max'),
            'postal_code.required' => __('merchant::validation.postal_code.required'),
            'postal_code.string' => __('merchant::validation.postal_code.string'),
            'postal_code.max' => __('merchant::validation.postal_code.max'),
            'postal_code.regex' => __('merchant::validation.postal_code.regex'),
            'latitude.required' => __('merchant::validation.latitude.required'),
            'latitude.numeric' => __('merchant::validation.latitude.numeric'),
            'latitude.between' => __('merchant::validation.latitude.between'),
            'longitude.required' => __('merchant::validation.longitude.required'),
            'longitude.numeric' => __('merchant::validation.longitude.numeric'),
            'longitude.between' => __('merchant::validation.longitude.between'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'status' => false,
            'message' => __('merchant::validation.failed'),
            'errors' => $validator->errors(),
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
