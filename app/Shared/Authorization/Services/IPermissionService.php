<?php

namespace App\Shared\Authorization\Services;

use Illuminate\Database\Eloquent\Collection;

interface IPermissionService
{
    /**
     * Check if user has permission
     */
    public function userHasPermission(string $userId, string $permissionSlug): bool;

    /**
     * Get user permissions
     */
    public function getUserPermissions(string $userId): Collection;

    /**
     * Check if role has permission
     */
    public function roleHasPermission(string $roleId, string $permissionSlug): bool;
}
