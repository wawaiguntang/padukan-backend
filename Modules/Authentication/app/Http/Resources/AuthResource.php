<?php

namespace Modules\Authentication\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Auth Resource
 *
 * Formats authentication response data
 */
class AuthResource extends JsonResource
{
    /**
     * The message key for the response
     *
     * @var string
     */
    protected string $messageKey;

    /**
     * Additional data to include in the response
     *
     * @var array
     */
    protected array $additionalData;

    /**
     * Create a new resource instance
     *
     * @param mixed $resource
     * @param string $messageKey
     * @param array $additionalData
     */
    public function __construct($resource = null, string $messageKey = '', array $additionalData = [])
    {
        parent::__construct($resource);
        $this->messageKey = $messageKey;
        $this->additionalData = $additionalData;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'status' => true,
            'message' => $this->messageKey ? __('authentication::' . $this->messageKey) : '',
        ];

        // Include user data if available
        if ($this->resource && isset($this->resource['user'])) {
            $data['user'] = new UserResource($this->resource['user']);
        }

        // Include token data if available
        if ($this->resource && isset($this->resource['access_token'])) {
            $data['access_token'] = $this->resource['access_token'];
        }

        if ($this->resource && isset($this->resource['refresh_token'])) {
            $data['refresh_token'] = $this->resource['refresh_token'];
        }

        if ($this->resource && isset($this->resource['token_type'])) {
            $data['token_type'] = $this->resource['token_type'];
        }

        if ($this->resource && isset($this->resource['expires_in'])) {
            $data['expires_in'] = $this->resource['expires_in'];
        }

        // Include additional data
        foreach ($this->additionalData as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}