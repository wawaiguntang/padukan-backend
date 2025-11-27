<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMerchantAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|regex:/^[0-9]{5}$/',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'street.required' => __('profile::validation.street_required'),
            'street.max' => __('profile::validation.street_too_long'),
            'city.required' => __('profile::validation.city_required'),
            'city.max' => __('profile::validation.city_too_long'),
            'province.required' => __('profile::validation.province_required'),
            'province.max' => __('profile::validation.province_too_long'),
            'postal_code.required' => __('profile::validation.postal_code_required'),
            'postal_code.regex' => __('profile::validation.postal_code_invalid'),
            'latitude.required' => __('profile::validation.latitude_required'),
            'latitude.numeric' => __('profile::validation.latitude_invalid'),
            'latitude.between' => __('profile::validation.latitude_out_of_range'),
            'longitude.required' => __('profile::validation.longitude_required'),
            'longitude.numeric' => __('profile::validation.longitude_invalid'),
            'longitude.between' => __('profile::validation.longitude_out_of_range'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'street' => 'Street Address',
            'city' => 'City',
            'province' => 'Province',
            'postal_code' => 'Postal Code',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }
}