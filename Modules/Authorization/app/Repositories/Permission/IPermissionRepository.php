<?php

namespace Modules\Authorization\Repositories\Permission;

use Modules\Authorization\Models\Permission;

/**
 * Interface for Permission Repository
 *
 * This interface defines the contract for permission data operations
 * in the authorization module.
 */
interface IPermissionRepository
{
    /**
     * Find a permission by its slug
     *
     * @param string $slug The permission slug
     * @return Permission|null The permission model if found, null otherwise
     */
    public function findBySlug(string $slug): ?Permission;

    /**
     * Find a permission by its ID
     *
     * @param int $id The permission ID
     * @return Permission|null The permission model if found, null otherwise
     */
    public function findById(int $id): ?Permission;

    /**
     * Get all permissions
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of permissions
     */
    public function getAll();

    /**
     * Get permissions by module
     *
     * @param string $module The module name
     * @return \Illuminate\Database\Eloquent\Collection Collection of permissions
     */
    public function getByModule(string $module);

    /**
     * Create a new permission
     *
     * @param array $data Permission data containing:
     * - name: string - Permission display name
     * - slug: string - Permission unique identifier
     * - description?: string - Permission description (optional)
     * - module?: string - Module this permission belongs to (optional)
     * @return Permission The created permission model
     */
    public function create(array $data): Permission;

    /**
     * Update an existing permission
     *
     * @param int $id The permission ID
     * @param array $data Permission data to update
     * @return bool True if update was successful, false otherwise
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a permission
     *
     * @param int $id The permission ID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(int $id): bool;

    /**
     * Check if a permission exists by slug
     *
     * @param string $slug The permission slug
     * @return bool True if permission exists, false otherwise
     */
    public function existsBySlug(string $slug): bool;

    /**
     * Get roles that have a specific permission
     *
     * @param int $permissionId The permission ID
     * @return \Illuminate\Database\Eloquent\Collection Collection of roles
     */
    public function getRoles(int $permissionId);

    /**
     * Search permissions by name or description
     *
     * @param string $query The search query
     * @return \Illuminate\Database\Eloquent\Collection Collection of permissions
     */
    public function search(string $query);
}