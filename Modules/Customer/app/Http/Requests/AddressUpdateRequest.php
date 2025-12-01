<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Customer\Enums\AddressTypeEnum;

/**
 * Address Update Request
 *
 * Validates address update data
 */
class AddressUpdateRequest extends FormRequest
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
            'type' => ['nullable', 'string', 'in:' . implode(',', array_column(AddressTypeEnum::cases(), 'value'))],
            'label' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]+$/'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_primary' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.in' => __('customer::validation.type.in'),
            'label.max' => __('customer::validation.label.max'),
            'street.max' => __('customer::validation.street.max'),
            'city.max' => __('customer::validation.city.max'),
            'province.max' => __('customer::validation.province.max'),
            'postal_code.max' => __('customer::validation.postal_code.max'),
            'postal_code.regex' => __('customer::validation.postal_code.regex'),
            'latitude.numeric' => __('customer::validation.latitude.numeric'),
            'latitude.between' => __('customer::validation.latitude.between'),
            'longitude.numeric' => __('customer::validation.longitude.numeric'),
            'longitude.between' => __('customer::validation.longitude.between'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'type' => __('customer::attributes.type'),
            'label' => __('customer::attributes.label'),
            'street' => __('customer::attributes.street'),
            'city' => __('customer::attributes.city'),
            'province' => __('customer::attributes.province'),
            'postal_code' => __('customer::attributes.postal_code'),
            'latitude' => __('customer::attributes.latitude'),
            'longitude' => __('customer::attributes.longitude'),
            'is_primary' => __('customer::attributes.is_primary'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from string fields
        if ($this->has('label')) {
            $this->merge(['label' => trim($this->label)]);
        }
        if ($this->has('street')) {
            $this->merge(['street' => trim($this->street)]);
        }
        if ($this->has('city')) {
            $this->merge(['city' => trim($this->city)]);
        }
        if ($this->has('province')) {
            $this->merge(['province' => trim($this->province)]);
        }
        if ($this->has('postal_code')) {
            $this->merge(['postal_code' => trim($this->postal_code)]);
        }
    }
}
