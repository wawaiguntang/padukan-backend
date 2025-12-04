<?php

namespace Modules\Merchant\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Merchant\Enums\BusinessCategoryEnum;

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
        return true; // Authorization is handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_name' => ['sometimes', 'string', 'max:255'],
            'business_description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'website' => ['sometimes', 'nullable', 'url', 'max:255'],
            'logo_file' => ['sometimes', 'file', 'mimes:jpeg,jpg,png', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'business_name.string' => __('merchant::validation.business_name.string'),
            'business_name.max' => __('merchant::validation.business_name.max'),
            'business_description.string' => __('merchant::validation.business_description.string'),
            'business_description.max' => __('merchant::validation.business_description.max'),
            'phone.string' => __('merchant::validation.phone.string'),
            'phone.max' => __('merchant::validation.phone.max'),
            'email.email' => __('merchant::validation.email.email'),
            'email.max' => __('merchant::validation.email.max'),
            'website.url' => __('merchant::validation.website.url'),
            'website.max' => __('merchant::validation.website.max'),
            'logo_file.file' => __('merchant::validation.merchant_verification.logo_file.file'),
            'logo_file.mimes' => __('merchant::validation.merchant_verification.logo_file.mimes'),
            'logo_file.max' => __('merchant::validation.merchant_verification.logo_file.max'),
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
