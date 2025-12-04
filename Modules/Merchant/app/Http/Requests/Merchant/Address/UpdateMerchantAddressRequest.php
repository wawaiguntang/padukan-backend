<?php

namespace Modules\Merchant\Http\Requests\Merchant\Address;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update Merchant Address Request
 *
 * Validates data for updating merchant address
 */
class UpdateMerchantAddressRequest extends FormRequest
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
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
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
            'street.required' => __('merchant::validation.street.required'),
            'city.required' => __('merchant::validation.city.required'),
            'province.required' => __('merchant::validation.province.required'),
            'country.required' => __('merchant::validation.country.required'),
            'postal_code.required' => __('merchant::validation.postal_code.required'),
            'latitude.required' => __('merchant::validation.latitude.required'),
            'longitude.required' => __('merchant::validation.longitude.required'),
            'latitude.between' => __('merchant::validation.latitude.between'),
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
