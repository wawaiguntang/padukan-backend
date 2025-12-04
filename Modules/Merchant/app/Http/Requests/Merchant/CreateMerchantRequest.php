<?php

namespace Modules\Merchant\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'business_description' => 'nullable|string|max:1000',
            'business_category' => 'required|string|in:food,mart,service',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'business_name.required' => __('merchant::validation.business_name_required'),
            'business_category.required' => __('merchant::validation.business_category_required'),
            'business_category.in' => __('merchant::validation.business_category_invalid'),
            'phone.required' => __('merchant::validation.phone_required'),
            'email.email' => __('merchant::validation.email_invalid'),
            'website.url' => __('merchant::validation.website_invalid'),
            'address.required' => __('merchant::validation.address_required'),
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
