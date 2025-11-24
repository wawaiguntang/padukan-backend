<?php

namespace Modules\Authorization\Repositories\RolePermission;

use Modules\Authorization\Models\RolePermission;

/**
 * Interface for Role Permission Repository
 *
 * This interface defines the contract for role-permission relationship
 * data operations in the authorization module.
 */
interface IRolePermissionRepository
{
    /**
     * Find a role-permission relationship by IDs
     *
     * @param int $roleId The role ID
     * @param int $permissionId The permission ID
     * @return RolePermission|null The relationship model if found, null otherwise
     */
    public function findByIds(int $roleId, int $permissionId): ?RolePermission;

    /**
     * Create a new role-permission relationship
     *
     * @param array $data Relationship data containing:
     * - role_id: int - The role ID
     * - permission_id: int - The permission ID
     * @return RolePermission The created relationship model
     */
    public function create(array $data): RolePermission;

    /**
     * Delete a role-permission relationship
     *
     * @param int $roleId The role ID
     * @param int $permissionId The permission ID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(int $roleId, int $permissionId): bool;

    /**
     * Check if a role-permission relationship exists
     *
     * @param int $roleId The role ID
     * @param int $permissionId The permission ID
     * @return bool True if relationship exists, false otherwise
     */
    public function exists(int $roleId, int $permissionId): bool;

    /**
     * Get all permissions for a specific role
     *
     * @param int $roleId The role ID
     * @return \Illuminate\Database\Eloquent\Collection Collection of role-permission relationships
     */
    public function getPermissionsByRole(int $roleId);

    /**
     * Get all roles for a specific permission
     *
     * @param int $permissionId The permission ID
     * @return \Illuminate\Database\Eloquent\Collection Collection of role-permission relationships
     */
    public function getRolesByPermission(int $permissionId);

    /**
     * Bulk create role-permission relationships
     *
     * @param array $relationships Array of relationship data
     * @return bool True if bulk creation was successful, false otherwise
     */
    public function bulkCreate(array $relationships): bool;

    /**
     * Bulk delete role-permission relationships
     *
     * @param array $relationships Array of relationship data to delete
     * @return bool True if bulk deletion was successful, false otherwise
     */
    public function bulkDelete(array $relationships): bool;
}