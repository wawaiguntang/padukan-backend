<?php

namespace Modules\Authorization\Services\Permission;

use Modules\Authorization\Models\Permission;
use Modules\Authorization\Repositories\Permission\IPermissionRepository;
use Modules\Authorization\Exceptions\PermissionNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PermissionService implements IPermissionService
{
    private IPermissionRepository $permissionRepository;

    public function __construct(IPermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Get permission by ID
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
        return $this->permissionRepository->userHasPermission($userId, $permissionSlug);
    }

    /**
     * Get role permissions
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
        return $this->permissionRepository->roleHasPermission($roleId, $permissionSlug);
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