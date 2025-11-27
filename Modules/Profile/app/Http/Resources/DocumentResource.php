<?php

namespace Modules\Profile\Http\Resources;

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
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'file_path' => $this->file_path,
            'file_url' => $this->file_url ?? null,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'meta' => $this->meta,
            'expiry_date' => $this->expiry_date?->toISOString(),
            'is_verified' => $this->is_verified,
            'verification_status' => $this->verification_status,
            'verified_at' => $this->verified_at?->toISOString(),
            'verified_by' => $this->verified_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}