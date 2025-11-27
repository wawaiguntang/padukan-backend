<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMerchantBankRequest extends FormRequest
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
            'bank_id' => 'required|uuid|exists:banks,id',
            'account_number' => 'required|string|regex:/^[0-9]+$/|min:10|max:20',
            'is_primary' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'bank_id.required' => __('profile::validation.bank_id_required'),
            'bank_id.uuid' => __('profile::validation.bank_id_invalid'),
            'bank_id.exists' => __('profile::validation.bank_not_found'),
            'account_number.required' => __('profile::validation.account_number_required'),
            'account_number.regex' => __('profile::validation.account_number_invalid'),
            'account_number.min' => __('profile::validation.account_number_too_short'),
            'account_number.max' => __('profile::validation.account_number_too_long'),
            'is_primary.boolean' => __('profile::validation.is_primary_invalid'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'bank_id' => 'Bank',
            'account_number' => 'Account Number',
            'is_primary' => 'Primary Account',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default value for is_primary if not provided
        if (!$this->has('is_primary')) {
            $this->merge(['is_primary' => false]);
        }
    }
}