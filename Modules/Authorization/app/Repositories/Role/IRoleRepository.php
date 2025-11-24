<?php

namespace Modules\Authorization\Repositories\Role;

use Modules\Authorization\Models\Role;

/**
 * Interface for Role Repository
 *
 * This interface defines the contract for role data operations
 * in the authorization module.
 */
interface IRoleRepository
{
    /**
     * Find a role by its slug
     *
     * @param string $slug The role slug
     * @return Role|null The role model if found, null otherwise
     */
    public function findBySlug(string $slug): ?Role;

    /**
     * Find a role by its ID
     *
     * @param int $id The role ID
     * @return Role|null The role model if found, null otherwise
     */
    public function findById(int $id): ?Role;

    /**
     * Get all roles
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of roles
     */
    public function getAll();

    /**
     * Get active roles only
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of active roles
     */
    public function getActive();

    /**
     * Create a new role
     *
     * @param array $data Role data containing:
     * - name: string - Role display name
     * - slug: string - Role unique identifier
     * - description?: string - Role description (optional)
     * @return Role The created role model
     */
    public function create(array $data): Role;

    /**
     * Update an existing role
     *
     * @param int $id The role ID
     * @param array $data Role data to update
     * @return bool True if update was successful, false otherwise
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a role
     *
     * @param int $id The role ID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(int $id): bool;

    /**
     * Check if a role exists by slug
     *
     * @param string $slug The role slug
     * @return bool True if role exists, false otherwise
     */
    public function existsBySlug(string $slug): bool;

    /**
     * Get permissions for a specific role
     *
     * @param int $roleId The role ID
     * @return \Illuminate\Database\Eloquent\Collection Collection of permissions
     */
    public function getPermissions(int $roleId);

    /**
     * Assign permissions to a role
     *
     * @param int $roleId The role ID
     * @param array $permissionIds Array of permission IDs
     * @return bool True if assignment was successful, false otherwise
     */
    public function assignPermissions(int $roleId, array $permissionIds): bool;

    /**
     * Remove permissions from a role
     *
     * @param int $roleId The role ID
     * @param array $permissionIds Array of permission IDs
     * @return bool True if removal was successful, false otherwise
     */
    public function removePermissions(int $roleId, array $permissionIds): bool;

    /**
     * Sync permissions for a role (replace all existing permissions)
     *
     * @param int $roleId The role ID
     * @param array $permissionIds Array of permission IDs
     * @return bool True if sync was successful, false otherwise
     */
    public function syncPermissions(int $roleId, array $permissionIds): bool;
}