<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitMerchantDocumentsRequest extends FormRequest
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
            // ID Card document
            'id_card_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120', // 5MB
            'id_card_number' => 'required|string|regex:/^[0-9]{16}$/',
            'id_card_expiry_date' => 'required|date|after:today',

            // Store/Business license document
            'store_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120', // 5MB
            'license_number' => 'required|string|max:255',
            'license_expiry_date' => 'required|date|after:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id_card_file.required' => __('profile::validation.id_card_file_required'),
            'id_card_file.mimes' => __('profile::validation.id_card_file_invalid_format'),
            'id_card_file.max' => __('profile::validation.file_too_large'),
            'id_card_number.required' => __('profile::validation.id_card_number_required'),
            'id_card_number.regex' => __('profile::validation.id_card_number_invalid'),
            'id_card_expiry_date.required' => __('profile::validation.id_card_expiry_required'),
            'id_card_expiry_date.after' => __('profile::validation.expiry_date_must_be_future'),

            'store_file.required' => __('profile::validation.store_file_required'),
            'store_file.mimes' => __('profile::validation.store_file_invalid_format'),
            'store_file.max' => __('profile::validation.file_too_large'),
            'license_number.required' => __('profile::validation.license_number_required'),
            'license_expiry_date.required' => __('profile::validation.license_expiry_required'),
            'license_expiry_date.after' => __('profile::validation.expiry_date_must_be_future'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'id_card_file' => 'ID Card File',
            'id_card_number' => 'ID Card Number',
            'id_card_expiry_date' => 'ID Card Expiry Date',
            'store_file' => 'Store License File',
            'license_number' => 'License Number',
            'license_expiry_date' => 'License Expiry Date',
        ];
    }
}