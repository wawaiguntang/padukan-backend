<?php

namespace Modules\Profile\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Addresses Response Resource
 *
 * Formats addresses response data for API responses
 */
class AddressesResponseResource extends JsonResource
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
                'addresses' => AddressResource::collection($this->resource['addresses']),
            ],
        ];
    }
}