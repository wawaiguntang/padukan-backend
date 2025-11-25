<?php

namespace App\Shared\Authorization\Repositories;

interface IPermissionRepository
{
    /**
     * Check if user has permission
     */
    public function userHasPermission(string $userId, string $permissionSlug): bool;

    /**
     * Get user permissions
     */
    public function getUserPermissions(string $userId): array;
}