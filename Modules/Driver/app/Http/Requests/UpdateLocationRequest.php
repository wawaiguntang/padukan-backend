<?php

namespace Modules\Driver\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update Location Request Validation
 */
class UpdateLocationRequest extends FormRequest
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
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'latitude.required' => __('driver::validation.status.latitude.required'),
            'latitude.numeric' => __('driver::validation.status.latitude.numeric'),
            'latitude.between' => __('driver::validation.status.latitude.between'),
            'longitude.required' => __('driver::validation.status.longitude.required'),
            'longitude.numeric' => __('driver::validation.status.longitude.numeric'),
            'longitude.between' => __('driver::validation.status.longitude.between'),
        ];
    }

    /**
     * Handle a failed validation attempt.
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
