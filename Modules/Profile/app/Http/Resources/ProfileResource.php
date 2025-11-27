<?php

namespace Modules\Profile\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Profile\Services\FileUpload\IFileUploadService;

/**
 * Profile Resource
 *
 * Formats profile data for API responses
 */
class ProfileResource extends JsonResource
{
    private IFileUploadService $fileUploadService;

    public function __construct($resource, IFileUploadService $fileUploadService = null)
    {
        parent::__construct($resource);
        $this->fileUploadService = $fileUploadService ?? app(IFileUploadService::class);
    }

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
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'avatar' => $this->avatar ? $this->fileUploadService->getFileUrl($this->avatar) : null,
            'gender' => $this->gender,
            'language' => $this->language,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}