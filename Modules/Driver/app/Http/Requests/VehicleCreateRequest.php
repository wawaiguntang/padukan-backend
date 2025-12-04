<?php

namespace Modules\Driver\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Vehicle Create Request Validation
 */
class VehicleCreateRequest extends FormRequest
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
            'type' => 'required|string|in:motorcycle,car',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'required|string|max:50',
            'license_plate' => 'required|string|max:20|unique:driver.vehicles,license_plate',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => __('driver::validation.vehicle.type.required'),
            'type.in' => __('driver::validation.vehicle.type.in'),
            'brand.required' => __('driver::validation.vehicle.brand.required'),
            'brand.max' => __('driver::validation.vehicle.brand.max'),
            'model.required' => __('driver::validation.vehicle.model.required'),
            'model.max' => __('driver::validation.vehicle.model.max'),
            'year.required' => __('driver::validation.vehicle.year.required'),
            'year.integer' => __('driver::validation.vehicle.year.integer'),
            'year.min' => __('driver::validation.vehicle.year.min'),
            'year.max' => __('driver::validation.vehicle.year.max'),
            'color.required' => __('driver::validation.vehicle.color.required'),
            'color.max' => __('driver::validation.vehicle.color.max'),
            'license_plate.required' => __('driver::validation.vehicle.license_plate.required'),
            'license_plate.max' => __('driver::validation.vehicle.license_plate.max'),
            'license_plate.unique' => __('driver::validation.vehicle.license_plate.unique'),
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
