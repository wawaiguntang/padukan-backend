<?php

namespace Modules\Authorization\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckRoleResource extends JsonResource
{
    private $roles;
    private ?string $appType;
    private string $userId;

    public function __construct($roles, string $userId, ?string $appType = null)
    {
        parent::__construct(null);
        $this->roles = $roles;
        $this->userId = $userId;
        $this->appType = $appType;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->userId,
            'roles' => $this->getRolesArray(),
            'app_type' => $this->appType,
        ];
    }

    /**
     * Get roles as array for response
     */
    private function getRolesArray(): array
    {
        return $this->roles->pluck('slug')->toArray();
    }
}