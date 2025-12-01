<?php

namespace Modules\Driver\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Vehicle Resource
 *
 * Formats driver vehicle data for API responses
 */
class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'driver_profile_id' => $this->driver_profile_id,
            'type' => $this->type,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'color' => $this->color,
            'license_plate' => $this->license_plate,
            'is_verified' => $this->is_verified,
            'verification_status' => $this->verification_status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Computed fields
            'full_name' => $this->when($this->brand && $this->model, function () {
                return $this->brand . ' ' . $this->model . ' ' . $this->year;
            }),
            'age' => $this->when($this->year, function () {
                return now()->year - $this->year;
            }),
            'is_active' => $this->is_verified,
        ];
    }
}
