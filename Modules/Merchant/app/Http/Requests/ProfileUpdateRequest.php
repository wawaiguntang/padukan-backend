<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Profile Update Request
 *
 * Validates profile update data
 */
class ProfileUpdateRequest extends FormRequest
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
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'language' => 'nullable|string|max:10',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.string' => __('merchant::validation.first_name.string'),
            'first_name.max' => __('merchant::validation.first_name.max'),
            'last_name.string' => __('merchant::validation.last_name.string'),
            'last_name.max' => __('merchant::validation.last_name.max'),
            'gender.in' => __('merchant::validation.gender.in'),
            'language.string' => __('merchant::validation.language.string'),
            'language.max' => __('merchant::validation.language.max'),
            'avatar_file.image' => __('merchant::validation.avatar_file.image'),
            'avatar_file.mimes' => __('merchant::validation.avatar_file.mimes'),
            'avatar_file.max' => __('merchant::validation.avatar_file.max'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert gender string to enum value if needed
        if ($this->has('gender') && $this->gender) {
            // Keep as string for now, will be cast in model
        }
    }
}
