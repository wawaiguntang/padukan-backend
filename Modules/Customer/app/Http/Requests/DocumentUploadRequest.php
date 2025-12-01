<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Customer\Enums\DocumentTypeEnum;

/**
 * Document Upload Request
 *
 * Validates document upload data
 */
class DocumentUploadRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:' . implode(',', array_column(DocumentTypeEnum::cases(), 'value'))],
            'file' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx,txt', 'max:10240'], // 10MB max
            'expiry_date' => ['nullable', 'date', 'after:today'],
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
            'file.required' => __('customer::validation.file.required'),
            'file.file' => __('customer::validation.file.file'),
            'file.mimes' => __('customer::validation.file.mimes'),
            'file.max' => __('customer::validation.file.max'),
            'expiry_date.date' => __('customer::validation.expiry_date.date'),
            'expiry_date.after' => __('customer::validation.expiry_date.after'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'type' => __('customer::attributes.type'),
            'file' => __('customer::attributes.file'),
            'expiry_date' => __('customer::attributes.expiry_date'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Add any preprocessing here if needed
    }
}
