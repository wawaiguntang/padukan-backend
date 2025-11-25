<?php

namespace Modules\Authorization\Services\Permission;

use Modules\Authorization\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

interface IPermissionService
{
    /**
     * Get permission by ID
     */
    public function getPermissionById(string $id): Permission;

    /**
     * Get permission by slug
     */
    public function getPermissionBySlug(string $slug): Permission;

    /**
     * Get all active permissions
     */
    public function getActivePermissions(): Collection;

    /**
     * Get user permissions
     */
    public function getUserPermissions(string $userId): Collection;

    /**
     * Check if user has permission
     */
    public function userHasPermission(string $userId, string $permissionSlug): bool;

    /**
     * Get role permissions
     */
    public function getRolePermissions(string $roleId): Collection;

    /**
     * Check if role has permission
     */
    public function roleHasPermission(string $roleId, string $permissionSlug): bool;

    /**
     * Get permissions by resource
     */
    public function getPermissionsByResource(string $resource): Collection;

    /**
     * Get permissions by action
     */
    public function getPermissionsByAction(string $action): Collection;

    /**
     * Create new permission
     */
    public function createPermission(array $data): Permission;

    /**
     * Update permission
     */
    public function updatePermission(string $id, array $data): bool;

    /**
     * Delete permission
     */
    public function deletePermission(string $id): bool;
}