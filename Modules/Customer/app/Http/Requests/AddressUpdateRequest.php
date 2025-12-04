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
