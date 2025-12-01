<?php

namespace Modules\Driver\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Driver\Services\FileUpload\IFileUploadService;

/**
 * Profile Resource
 *
 * Formats driver profile data for API responses
 */
class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->when($this->first_name || $this->last_name, function () {
                return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
            }),
            'avatar' => $this->when($this->avatar, function () {
                return app(IFileUploadService::class)->getAvatarUrl($this->avatar);
            }),
            'avatar_path' => $this->avatar,
            'gender' => $this->gender?->value,
            'gender_label' => $this->gender?->label(),
            'language' => $this->language,
            'is_verified' => $this->is_verified,
            'verification_status' => $this->verification_status,
            'verified_services' => $this->verified_services,
            'available_services' => $this->getAvailableServices(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'vehicles' => VehicleResource::collection($this->whenLoaded('vehicles')),
            'driver_status' => $this->whenLoaded('driverStatus', function () {
                return [
                    'online_status' => $this->driverStatus->online_status->value,
                    'operational_status' => $this->driverStatus->operational_status->value,
                    'active_service' => $this->driverStatus->active_service?->value,
                    'location' => $this->driverStatus->latitude && $this->driverStatus->longitude ? [
                        'latitude' => $this->driverStatus->latitude,
                        'longitude' => $this->driverStatus->longitude,
                    ] : null,
                    'last_updated_at' => $this->driverStatus->last_updated_at,
                ];
            }),
        ];
    }
}
