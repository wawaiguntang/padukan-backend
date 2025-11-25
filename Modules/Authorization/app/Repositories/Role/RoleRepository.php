<?php

namespace Modules\Authorization\Repositories\Role;

use Modules\Authorization\Models\Role;
use Modules\Authorization\Models\UserRole;
use Modules\Authorization\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoleRepository implements IRoleRepository
{
    /**
     * Find role by ID
     */
    public function findById(string $id): ?Role
    {
        return Role::find($id);
    }

    /**
     * Find role by slug
     */
    public function findBySlug(string $slug): ?Role
    {
        return Role::where('slug', $slug)->first();
    }

    /**
     * Get all active roles
     */
    public function getActiveRoles(): Collection
    {
        return Role::active()->get();
    }

    /**
     * Get roles for user
     */
    public function getUserRoles(string $userId): Collection
    {
        return Role::whereHas('userRoles', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    }

    /**
     * Check if user has role
     */
    public function userHasRole(string $userId, string $roleSlug): bool
    {
        return UserRole::where('user_id', $userId)
                      ->whereHas('role', function ($query) use ($roleSlug) {
                          $query->where('slug', $roleSlug);
                      })
                      ->exists();
    }

    /**
     * Assign role to user
     */
    public function assignRoleToUser(string $userId, string $roleSlug): bool
    {
        $role = $this->findBySlug($roleSlug);
        if (!$role) {
            return false;
        }

        // Check if already assigned
        if ($this->userHasRole($userId, $roleSlug)) {
            return true;
        }

        try {
            UserRole::create([
                'user_id' => $userId,
                'role_id' => $role->id
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(string $userId, string $roleSlug): bool
    {
        $role = $this->findBySlug($roleSlug);
        if (!$role) {
            return false;
        }

        return UserRole::where('user_id', $userId)
                      ->where('role_id', $role->id)
                      ->delete() > 0;
    }

    /**
     * Get role permissions
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
}