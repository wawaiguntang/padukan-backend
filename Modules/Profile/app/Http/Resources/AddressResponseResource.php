<?php

namespace Modules\Profile\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Address Response Resource
 *
 * Formats address response data for API responses
 */
class AddressResponseResource extends JsonResource
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
                'address' => new AddressResource($this->resource['address']),
            ],
        ];
    }
}