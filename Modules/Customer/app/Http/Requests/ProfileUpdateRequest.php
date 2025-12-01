<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Customer\Enums\GenderEnum;

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
        return true; // Authorization is handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'url', 'max:2048'],
            'gender' => ['nullable', 'string', 'in:' . implode(',', array_column(GenderEnum::cases(), 'value'))],
            'language' => ['nullable', 'string', 'in:id,en'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.string' => __('customer::validation.first_name.string'),
            'first_name.max' => __('customer::validation.first_name.max'),
            'last_name.string' => __('customer::validation.last_name.string'),
            'last_name.max' => __('customer::validation.last_name.max'),
            'avatar.url' => __('customer::validation.avatar.url'),
            'avatar.max' => __('customer::validation.avatar.max'),
            'gender.in' => __('customer::validation.gender.in'),
            'language.in' => __('customer::validation.language.in'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => __('customer::attributes.first_name'),
            'last_name' => __('customer::attributes.last_name'),
            'avatar' => __('customer::attributes.avatar'),
            'gender' => __('customer::attributes.gender'),
            'language' => __('customer::attributes.language'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from string fields
        $this->merge([
            'first_name' => $this->first_name ? trim($this->first_name) : null,
            'last_name' => $this->last_name ? trim($this->last_name) : null,
            'avatar' => $this->avatar ? trim($this->avatar) : null,
        ]);
    }
}
