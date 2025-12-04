<?php

namespace Modules\Merchant\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update Merchant Request
 *
 * Validates data for updating a merchant
 */
class UpdateMerchantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_name' => 'sometimes|string|max:255',
            'business_description' => 'sometimes|nullable|string|max:1000',
            'business_category' => 'sometimes|string|in:food,mart,service',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|nullable|email|max:255',
            'website' => 'sometimes|nullable|url|max:255',
            'address' => 'sometimes|string|max:500',
            'latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'longitude' => 'sometimes|nullable|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'business_name.string' => __('merchant::validation.business_name_string'),
            'business_category.in' => __('merchant::validation.business_category_invalid'),
            'email.email' => __('merchant::validation.email_invalid'),
            'website.url' => __('merchant::validation.website_invalid'),
            'latitude.between' => __('merchant::validation.latitude_invalid'),
            'longitude.between' => __('merchant::validation.longitude_invalid'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'business_name' => __('merchant::validation.attributes.business_name'),
            'business_description' => __('merchant::validation.attributes.business_description'),
            'business_category' => __('merchant::validation.attributes.business_category'),
            'phone' => __('merchant::validation.attributes.phone'),
            'email' => __('merchant::validation.attributes.email'),
            'website' => __('merchant::validation.attributes.website'),
            'address' => __('merchant::validation.attributes.address'),
            'latitude' => __('merchant::validation.attributes.latitude'),
            'longitude' => __('merchant::validation.attributes.longitude'),
        ];
    }
}
