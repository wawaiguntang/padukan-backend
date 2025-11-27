<?php

namespace Modules\Authorization\Services\Role;

use Modules\Authorization\Models\Role;
use Modules\Authorization\Repositories\Role\IRoleRepository;
use Modules\Authorization\Exceptions\RoleNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoleService implements IRoleService
{
    private IRoleRepository $roleRepository;

    public function __construct(IRoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Get role by ID
     *
     * @cache-category Basic Data Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.lookup_ttl') - 1 hour
     * @cache-key authorization:role:id:{id}
     * @cache-invalidation When role is updated/deleted
     */
    public function getRoleById(string $id): Role
    {
        $role = $this->roleRepository->findById($id);

        if (!$role) {
            throw new RoleNotFoundException('role.not_found', ['role_id' => $id]);
        }
        return $role;
    }

    /**
     * Get role by slug
     *
     * @cache-category Basic Data Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.lookup_ttl') - 1 hour
     * @cache-key authorization:role:slug:{slug}
     * @cache-invalidation When role is updated/deleted
     */
    public function getRoleBySlug(string $slug): Role
    {
        $role = $this->roleRepository->findBySlug($slug);

        if (!$role) {
            throw new RoleNotFoundException('role.not_found', ['role_slug' => $slug]);
        }
        return $role;
    }

    /**
     * Get all active roles
     */
    public function getActiveRoles(): Collection
    {
        return $this->roleRepository->getActiveRoles();
    }

    /**
     * Get user roles
     *
     * @cache-category Business Logic Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.user_roles_ttl') - 1 hour
     * @cache-key authorization:user:{userId}:roles
     * @cache-invalidation When user role assignments change
     */
    public function getUserRoles(string $userId): Collection
    {
        return $this->roleRepository->getUserRoles($userId);
    }

    /**
     * Check if user has role
     */
    public function userHasRole(string $userId, string $roleSlug): bool
    {
        // Use cached user roles for better performance
        $userRoles = $this->getUserRoles($userId);
        return $userRoles->contains('slug', $roleSlug);
    }

    /**
     * Assign role to user
     */
    public function assignRoleToUser(string $userId, string $roleSlug): bool
    {
        return $this->roleRepository->assignRoleToUser($userId, $roleSlug);
    }

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(string $userId, string $roleSlug): bool
    {
        return $this->roleRepository->removeRoleFromUser($userId, $roleSlug);
    }

    /**
     * Get role permissions
     */
    public function getRolePermissions(string $roleId): Collection
    {
        return $this->roleRepository->getRolePermissions($roleId);
    }

    /**
     * Check if role has permission
     */
    public function roleHasPermission(string $roleId, string $permissionSlug): bool
    {
        return $this->roleRepository->roleHasPermission($roleId, $permissionSlug);
    }


    /**
     * Create new role
     */
    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            return Role::create($data);
        });
    }

    /**
     * Update role
     */
    public function updateRole(string $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $role = $this->getRoleById($id);
            return $role->update($data);
        });
    }

    /**
     * Delete role
     */
    public function deleteRole(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $role = $this->getRoleById($id);
            return $role->delete();
        });
    }
}