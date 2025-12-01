<?php

namespace Modules\Driver\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Driver\Services\FileUpload\IFileUploadService;

/**
 * Document Resource
 *
 * Formats driver document data for API responses
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
            'profile_id' => $this->profile_id,
            'type' => $this->type?->value,
            'type_label' => $this->type?->label(),
            'file_path' => $this->file_path,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'temporary_url' => $this->when($this->file_path, function () {
                return app(IFileUploadService::class)->generateTemporaryUrl($this->file_path);
            }),
            'meta' => $this->meta,
            'expiry_date' => $this->expiry_date?->toISOString(),
            'is_verified' => $this->is_verified,
            'verification_status' => $this->verification_status,
            'verified_at' => $this->verified_at?->toISOString(),
            'verified_by' => $this->verified_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Computed fields
            'is_expired' => $this->when($this->expiry_date, function () {
                return $this->expiry_date->isPast();
            }),
            'days_until_expiry' => $this->when($this->expiry_date, function () {
                return $this->expiry_date->isFuture() ? now()->diffInDays($this->expiry_date) : 0;
            }),
        ];
    }
}
