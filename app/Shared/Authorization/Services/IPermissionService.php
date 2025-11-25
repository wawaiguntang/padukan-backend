<?php

namespace App\Shared\Authorization\Services;

interface IPermissionService
{
    /**
     * Check if user has permission
     */
    public function userHasPermission(string $userId, string $permissionSlug): bool;

    /**
     * Get user permissions
     */
    public function getUserPermissions(string $userId): array;

    /**
     * Check if role has permission
     */
    public function roleHasPermission(string $roleId, string $permissionSlug): bool;
}