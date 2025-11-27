<?php

namespace Modules\Profile\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Documents Response Resource
 *
 * Formats documents response data for API responses
 */
class DocumentsResponseResource extends JsonResource
{
    private string $message;

    public function __construct($resource, string $message)
    {
        parent::__construct($resource);
        $this->message = $message;
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
            'status' => true,
            'message' => __('profile::' . $this->message),
            'data' => [
                'profile' => new ProfileResource($this->resource['profile']),
                'customer_profile' => new CustomerProfileResource($this->resource['customer_profile']),
                'documents' => DocumentResource::collection($this->resource['documents']),
            ],
        ];
    }
}