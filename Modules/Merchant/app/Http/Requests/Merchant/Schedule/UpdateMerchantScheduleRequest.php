<?php

namespace Modules\Merchant\Http\Requests\Merchant\Schedule;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update Merchant Schedule Request
 *
 * Validates data for updating merchant schedule
 */
class UpdateMerchantScheduleRequest extends FormRequest
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
            'regular_hours' => 'required|array',
            'regular_hours.*.open' => 'required|string|regex:/^\d{2}:\d{2}$/',
            'regular_hours.*.close' => 'required|string|regex:/^\d{2}:\d{2}$/',
            'regular_hours.*.is_open' => 'required|boolean',
            'special_schedules' => 'nullable|array',
            'special_schedules.*.date' => 'required|date',
            'special_schedules.*.name' => 'required|string|max:255',
            'special_schedules.*.is_open' => 'required|boolean',
            'special_schedules.*.open_time' => 'nullable|string|regex:/^\d{2}:\d{2}$/',
            'special_schedules.*.close_time' => 'nullable|string|regex:/^\d{2}:\d{2}$/',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'regular_hours.required' => __('merchant::validation.regular_hours.required'),
            'regular_hours.array' => __('merchant::validation.regular_hours.array'),
            'regular_hours.*.open.required' => __('merchant::validation.regular_hours.*.open.required'),
            'regular_hours.*.close.required' => __('merchant::validation.regular_hours.*.close.required'),
            'regular_hours.*.is_open.required' => __('merchant::validation.regular_hours.*.is_open.required'),
            'special_schedules.array' => __('merchant::validation.special_schedules.array'),
            'special_schedules.*.date.required' => __('merchant::validation.special_schedules.*.date.required'),
            'special_schedules.*.name.required' => __('merchant::validation.special_schedules.*.name.required'),
            'special_schedules.*.is_open.required' => __('merchant::validation.special_schedules.*.is_open.required'),
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
