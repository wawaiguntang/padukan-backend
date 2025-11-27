<?php

namespace Modules\Profile\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Profile Response Resource
 *
 * Formats profile response data with customizable messages and additional data
 */
class ProfileResponseResource extends JsonResource
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
            'message' => $this->messageKey ? __('profile::messages.' . $this->messageKey) : '',
        ];

        // Include profile data if available
        if ($this->resource && isset($this->resource['profile'])) {
            $data['profile'] = new ProfileResource($this->resource['profile']);
        }

        // Include customer profile data if available
        if ($this->resource && isset($this->resource['customer_profile'])) {
            $data['customer_profile'] = new CustomerProfileResource($this->resource['customer_profile']);
        }

        // Include additional data
        foreach ($this->additionalData as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}