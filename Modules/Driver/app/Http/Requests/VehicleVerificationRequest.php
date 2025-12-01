<?php

namespace Modules\Driver\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Vehicle Verification Request
 *
 * Validates vehicle verification submission with required documents
 */
class VehicleVerificationRequest extends FormRequest
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
            // Vehicle ID (required for verification)
            'vehicle_id' => 'required|string|exists:driver.vehicles,id',

            // SIM document
            'sim_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'sim_meta' => 'required|array',
            'sim_meta.number' => 'required|string|max:255',
            'sim_expiry_date' => 'required|date|after:today',

            // STNK document
            'stnk_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'stnk_expiry_date' => 'required|date|after:today',

            // Vehicle photos (multiple angles)
            'vehicle_photos' => 'required|array|min:4|max:4', // front, back, left, right
            'vehicle_photos.*' => 'required|file|mimes:jpeg,jpg,png|max:5120',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vehicle_id.required' => __('driver::validation.vehicle_verification.vehicle_id.required'),
            'vehicle_id.exists' => __('driver::validation.vehicle_verification.vehicle_id.exists'),

            'sim_file.required' => __('driver::validation.vehicle_verification.sim_file.required'),
            'sim_file.file' => __('driver::validation.vehicle_verification.sim_file.file'),
            'sim_file.mimes' => __('driver::validation.vehicle_verification.sim_file.mimes'),
            'sim_file.max' => __('driver::validation.vehicle_verification.sim_file.max'),

            'sim_meta.required' => __('driver::validation.vehicle_verification.sim_meta.required'),
            'sim_meta.array' => __('driver::validation.vehicle_verification.sim_meta.array'),
            'sim_meta.number.required' => __('driver::validation.vehicle_verification.sim_meta.number.required'),

            'sim_expiry_date.required' => __('driver::validation.vehicle_verification.sim_expiry_date.required'),
            'sim_expiry_date.date' => __('driver::validation.vehicle_verification.sim_expiry_date.date'),
            'sim_expiry_date.after' => __('driver::validation.vehicle_verification.sim_expiry_date.after'),

            'stnk_file.required' => __('driver::validation.vehicle_verification.stnk_file.required'),
            'stnk_file.file' => __('driver::validation.vehicle_verification.stnk_file.file'),
            'stnk_file.mimes' => __('driver::validation.vehicle_verification.stnk_file.mimes'),
            'stnk_file.max' => __('driver::validation.vehicle_verification.stnk_file.max'),

            'stnk_expiry_date.required' => __('driver::validation.vehicle_verification.stnk_expiry_date.required'),
            'stnk_expiry_date.date' => __('driver::validation.vehicle_verification.stnk_expiry_date.date'),
            'stnk_expiry_date.after' => __('driver::validation.vehicle_verification.stnk_expiry_date.after'),

            'vehicle_photos.required' => __('driver::validation.vehicle_verification.vehicle_photos.required'),
            'vehicle_photos.array' => __('driver::validation.vehicle_verification.vehicle_photos.array'),
            'vehicle_photos.min' => __('driver::validation.vehicle_verification.vehicle_photos.min'),
            'vehicle_photos.max' => __('driver::validation.vehicle_verification.vehicle_photos.max'),
            'vehicle_photos.*.file' => __('driver::validation.vehicle_verification.vehicle_photos.file'),
            'vehicle_photos.*.mimes' => __('driver::validation.vehicle_verification.vehicle_photos.mimes'),
            'vehicle_photos.*.max' => __('driver::validation.vehicle_verification.vehicle_photos.max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'vehicle_id' => __('driver::attributes.vehicle_verification.vehicle_id'),
            'sim_file' => __('driver::attributes.vehicle_verification.sim_file'),
            'sim_meta' => __('driver::attributes.vehicle_verification.sim_meta'),
            'sim_meta.number' => __('driver::attributes.vehicle_verification.sim_meta.number'),
            'sim_expiry_date' => __('driver::attributes.vehicle_verification.sim_expiry_date'),
            'stnk_file' => __('driver::attributes.vehicle_verification.stnk_file'),
            'stnk_expiry_date' => __('driver::attributes.vehicle_verification.stnk_expiry_date'),
            'vehicle_photos' => __('driver::attributes.vehicle_verification.vehicle_photos'),
            'vehicle_photos.*' => __('driver::attributes.vehicle_verification.vehicle_photo'),
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
