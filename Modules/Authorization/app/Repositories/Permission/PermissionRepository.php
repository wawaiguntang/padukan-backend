<?php

namespace Modules\Authorization\Repositories\Permission;

use Modules\Authorization\Models\Permission;
use Modules\Authorization\Models\UserRole;
use Modules\Authorization\Models\RolePermission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PermissionRepository implements IPermissionRepository
{
    /**
     * Find permission by ID
     */
    public function findById(string $id): ?Permission
    {
        return Permission::find($id);
    }

    /**
     * Find permission by slug
     */
    public function findBySlug(string $slug): ?Permission
    {
        return Permission::where('slug', $slug)->first();
    }

    /**
     * Get all active permissions
     */
    public function getActivePermissions(): Collection
    {
        return Permission::active()->get();
    }

    /**
     * Get permissions for user
     */
    public function getUserPermissions(string $userId): Collection
    {
        return Permission::whereHas('rolePermissions', function ($query) use ($userId) {
            $query->whereHas('role', function ($subQuery) use ($userId) {
                $subQuery->whereHas('userRoles', function ($subSubQuery) use ($userId) {
                    $subSubQuery->where('user_id', $userId);
                });
            });
        })->get();
    }

    /**
     * Check if user has permission
     */
    public function userHasPermission(string $userId, string $permissionSlug): bool
    {
        return Permission::where('slug', $permissionSlug)
                        ->whereHas('rolePermissions', function ($query) use ($userId) {
                            $query->whereHas('role', function ($subQuery) use ($userId) {
                                $subQuery->whereHas('userRoles', function ($subSubQuery) use ($userId) {
                                    $subSubQuery->where('user_id', $userId);
                                });
                            });
                        })
                        ->exists();
    }

    /**
     * Get permissions for role
     */
    public function getRolePermissions(string $roleId): Collection
    {
        return Permission::whereHas('rolePermissions', function ($query) use ($roleId) {
            $query->where('role_id', $roleId);
        })->get();
    }

    /**
     * Check if role has permission
     */
    public function roleHasPermission(string $roleId, string $permissionSlug): bool
    {
        return Permission::where('slug', $permissionSlug)
                        ->whereHas('rolePermissions', function ($query) use ($roleId) {
                            $query->where('role_id', $roleId);
                        })
                        ->exists();
    }

    /**
     * Get permissions by resource
     */
    public function getPermissionsByResource(string $resource): Collection
    {
        return Permission::forResource($resource)->active()->get();
    }

    /**
     * Get permissions by action
     */
    public function getPermissionsByAction(string $action): Collection
    {
        return Permission::forAction($action)->active()->get();
    }
}