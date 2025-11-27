<?php

namespace Modules\Authorization\Services\Permission;

use Modules\Authorization\Models\Permission;
use Modules\Authorization\Repositories\Permission\IPermissionRepository;
use Modules\Authorization\Exceptions\PermissionNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Shared\Authorization\Services\IPermissionService as SharedIPermissionService;

class PermissionService implements IPermissionService, SharedIPermissionService
{
    private IPermissionRepository $permissionRepository;

    public function __construct(IPermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Get permission by ID
     *
     * @cache-category Basic Data Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.lookup_ttl') - 1 hour
     * @cache-key authorization:permission:id:{id}
     * @cache-invalidation When permission is updated/deleted
     */
    public function getPermissionById(string $id): Permission
    {
        $permission = $this->permissionRepository->findById($id);

        if (!$permission) {
            throw new PermissionNotFoundException('permission.not_found', ['permission_id' => $id]);
        }
        return $permission;
    }

    /**
     * Get permission by slug
     */
    public function getPermissionBySlug(string $slug): Permission
    {
        $permission = $this->permissionRepository->findBySlug($slug);

        if (!$permission) {
            throw new PermissionNotFoundException('permission.not_found', ['permission_slug' => $slug]);
        }
        return $permission;
    }

    /**
     * Get all active permissions
     */
    public function getActivePermissions(): Collection
    {
        return $this->permissionRepository->getActivePermissions();
    }

    /**
     * Get user permissions
     *
     * @cache-category Business Logic Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.user_permissions_ttl') - 1 hour
     * @cache-key authorization:user:{userId}:permissions
     * @cache-invalidation When user role assignments change
     */
    public function getUserPermissions(string $userId): Collection
    {
        return $this->permissionRepository->getUserPermissions($userId);
    }

    /**
     * Check if user has permission
     */
    public function userHasPermission(string $userId, string $permissionSlug): bool
    {
        // Use cached user permissions for better performance
        $userPermissions = $this->getUserPermissions($userId);
        return $userPermissions->contains('slug', $permissionSlug);
    }

    /**
     * Get role permissions
     *
     * @cache-category Business Logic Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.role_permissions_ttl') - 1 hour
     * @cache-key authorization:role:{roleId}:permissions
     * @cache-invalidation When role permission assignments change
     */
    public function getRolePermissions(string $roleId): Collection
    {
        return $this->permissionRepository->getRolePermissions($roleId);
    }

    /**
     * Check if role has permission
     */
    public function roleHasPermission(string $roleId, string $permissionSlug): bool
    {
        // Use cached role permissions for better performance
        $rolePermissions = $this->getRolePermissions($roleId);
        return $rolePermissions->contains('slug', $permissionSlug);
    }

    /**
     * Get permissions by resource
     */
    public function getPermissionsByResource(string $resource): Collection
    {
        return $this->permissionRepository->getPermissionsByResource($resource);
    }

    /**
     * Get permissions by action
     */
    public function getPermissionsByAction(string $action): Collection
    {
        return $this->permissionRepository->getPermissionsByAction($action);
    }

    /**
     * Create new permission
     */
    public function createPermission(array $data): Permission
    {
        return DB::transaction(function () use ($data) {
            return Permission::create($data);
        });
    }

    /**
     * Update permission
     */
    public function updatePermission(string $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $permission = $this->getPermissionById($id);
            if (!$permission) {
                return false;
            }

            return $permission->update($data);
        });
    }

    /**
     * Delete permission
     */
    public function deletePermission(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $permission = $this->getPermissionById($id);
            if (!$permission) {
                return false;
            }

            return $permission->delete();
        });
    }

}
