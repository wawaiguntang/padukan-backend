<?php

namespace Modules\Merchant\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Merchant Verification Request
 *
 * Validates merchant verification submission with documents
 */
class MerchantVerificationRequest extends FormRequest
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
            // Merchant document file
            'merchant_document_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120', // 5MB max
            'merchant_document_meta' => 'sometimes|array',

            // Banner file
            'banner_file' => 'required|file|mimes:jpeg,jpg,png|max:5120', // 5MB max
            'banner_meta' => 'sometimes|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'merchant_document_file.required' => __('merchant::validation.merchant_verification.merchant_document_file.required'),
            'merchant_document_file.file' => __('merchant::validation.merchant_verification.merchant_document_file.file'),
            'merchant_document_file.mimes' => __('merchant::validation.merchant_verification.merchant_document_file.mimes'),
            'merchant_document_file.max' => __('merchant::validation.merchant_verification.merchant_document_file.max'),

            'merchant_document_meta.array' => __('merchant::validation.merchant_verification.merchant_document_meta.array'),

            'banner_file.required' => __('merchant::validation.merchant_verification.banner_file.required'),
            'banner_file.file' => __('merchant::validation.merchant_verification.banner_file.file'),
            'banner_file.mimes' => __('merchant::validation.merchant_verification.banner_file.mimes'),
            'banner_file.max' => __('merchant::validation.merchant_verification.banner_file.max'),

            'banner_meta.array' => __('merchant::validation.merchant_verification.banner_meta.array'),
        ];
    }

    /**
     * Handle a failed validation attempt.
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
