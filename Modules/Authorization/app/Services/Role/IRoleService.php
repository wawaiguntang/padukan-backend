<?php

namespace Modules\Authorization\Services\Role;

use Modules\Authorization\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface IRoleService
{
    /**
     * Get role by ID
     */
    public function getRoleById(string $id): Role;

    /**
     * Get role by slug
     */
    public function getRoleBySlug(string $slug): Role;

    /**
     * Get all active roles
     */
    public function getActiveRoles(): Collection;

    /**
     * Get user roles
     */
    public function getUserRoles(string $userId): Collection;

    /**
     * Check if user has role
     */
    public function userHasRole(string $userId, string $roleSlug): bool;

    /**
     * Assign role to user
     */
    public function assignRoleToUser(string $userId, string $roleSlug): bool;

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(string $userId, string $roleSlug): bool;

    /**
     * Get role permissions
     */
    public function getRolePermissions(string $roleId): Collection;

    /**
     * Check if role has permission
     */
    public function roleHasPermission(string $roleId, string $permissionSlug): bool;

    /**
     * Create new role
     */
    public function createRole(array $data): Role;

    /**
     * Update role
     */
    public function updateRole(string $id, array $data): bool;

    /**
     * Delete role
     */
    public function deleteRole(string $id): bool;
}