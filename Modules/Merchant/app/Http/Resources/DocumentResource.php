<?php

namespace Modules\Merchant\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Document Resource
 *
 * Formats document data for API responses
 */
class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type?->value,
            'type_label' => $this->type?->label(),
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'file_size_formatted' => $this->when($this->file_size, function () {
                $units = ['B', 'KB', 'MB', 'GB'];
                $bytes = $this->file_size;
                $i = 0;
                while ($bytes >= 1024 && $i < count($units) - 1) {
                    $bytes /= 1024;
                    $i++;
                }
                return round($bytes, 2) . ' ' . $units[$i];
            }),
            'temporary_url' => $this->when($this->file_path, function () {
                return app(\Modules\Merchant\Services\FileUpload\IFileUploadService::class)->generateTemporaryUrl($this->file_path);
            }),
            'expiry_date' => $this->expiry_date?->toISOString(),
            'is_expired' => $this->when($this->expiry_date, function () {
                return $this->expiry_date->isPast();
            }),
            'is_verified' => $this->is_verified,
            'verification_status' => $this->verification_status?->value,
            'verification_status_label' => $this->verification_status?->label(),
            'verification_color' => $this->verification_status?->color(),
            'verified_at' => $this->verified_at?->toISOString(),
            'verified_by' => $this->verified_by,
            'meta' => $this->meta,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'profile' => new ProfileResource($this->whenLoaded('merchantProfile')),
        ];
    }
}
