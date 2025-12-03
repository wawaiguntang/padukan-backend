<?php

namespace Modules\Driver\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Profile Verification Request
 *
 * Validates profile verification submission with ID card
 */
class ProfileVerificationRequest extends FormRequest
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
            // ID Card (KTP) file
            'id_card_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120', // 5MB max
            'id_card_meta' => 'required|array',
            'id_card_meta.name' => 'required|string|max:255',
            'id_card_meta.number' => 'required|string|max:255',
            'id_card_expiry_date' => 'sometimes|date|after:today',

            // Selfie with ID card file
            'selfie_with_id_card_file' => 'required|file|mimes:jpeg,jpg,png|max:5120', // 5MB max
            'selfie_with_id_card_meta' => 'sometimes|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id_card_file.required' => __('driver::validation.profile_verification.id_card_file.required'),
            'id_card_file.file' => __('driver::validation.profile_verification.id_card_file.file'),
            'id_card_file.mimes' => __('driver::validation.profile_verification.id_card_file.mimes'),
            'id_card_file.max' => __('driver::validation.profile_verification.id_card_file.max'),

            'id_card_meta.required' => __('driver::validation.profile_verification.id_card_meta.required'),
            'id_card_meta.array' => __('driver::validation.profile_verification.id_card_meta.array'),
            'id_card_meta.name.required' => __('driver::validation.profile_verification.id_card_meta.name.required'),
            'id_card_meta.number.required' => __('driver::validation.profile_verification.id_card_meta.number.required'),

            'id_card_expiry_date.date' => __('driver::validation.profile_verification.id_card_expiry_date.date'),
            'id_card_expiry_date.after' => __('driver::validation.profile_verification.id_card_expiry_date.after'),

            'selfie_with_id_card_file.required' => __('driver::validation.profile_verification.selfie_with_id_card_file.required'),
            'selfie_with_id_card_file.file' => __('driver::validation.profile_verification.selfie_with_id_card_file.file'),
            'selfie_with_id_card_file.mimes' => __('driver::validation.profile_verification.selfie_with_id_card_file.mimes'),
            'selfie_with_id_card_file.max' => __('driver::validation.profile_verification.selfie_with_id_card_file.max'),

            'selfie_with_id_card_meta.array' => __('driver::validation.profile_verification.selfie_with_id_card_meta.array'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'id_card_file' => __('driver::attributes.profile_verification.id_card_file'),
            'id_card_meta' => __('driver::attributes.profile_verification.id_card_meta'),
            'id_card_meta.name' => __('driver::attributes.profile_verification.id_card_meta.name'),
            'id_card_meta.number' => __('driver::attributes.profile_verification.id_card_meta.number'),
            'id_card_expiry_date' => __('driver::attributes.profile_verification.id_card_expiry_date'),
            'selfie_with_id_card_file' => __('driver::attributes.profile_verification.selfie_with_id_card_file'),
            'selfie_with_id_card_meta' => __('driver::attributes.profile_verification.selfie_with_id_card_meta'),
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
            'message' => __('driver::validation.failed'),
            'errors' => $validator->errors(),
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
