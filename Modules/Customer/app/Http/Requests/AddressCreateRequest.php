<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Customer\Enums\AddressTypeEnum;

/**
 * Address Create Request
 *
 * Validates address creation data
 */
class AddressCreateRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:' . implode(',', array_column(AddressTypeEnum::cases(), 'value'))],
            'label' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:10', 'regex:/^[0-9]+$/'],
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
            'type.required' => __('customer::validation.type.required'),
            'type.in' => __('customer::validation.type.in'),
            'label.required' => __('customer::validation.label.required'),
            'label.max' => __('customer::validation.label.max'),
            'street.required' => __('customer::validation.street.required'),
            'street.max' => __('customer::validation.street.max'),
            'city.required' => __('customer::validation.city.required'),
            'city.max' => __('customer::validation.city.max'),
            'province.required' => __('customer::validation.province.required'),
            'province.max' => __('customer::validation.province.max'),
            'postal_code.required' => __('customer::validation.postal_code.required'),
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
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'status' => false,
            'message' => __('customer::validation.failed'),
            'errors' => $validator->errors(),
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
