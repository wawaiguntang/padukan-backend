<?php

namespace Modules\Authorization\Repositories\Role;

use Modules\Authorization\Models\Role;
use Modules\Authorization\Models\UserRole;
use Modules\Authorization\Models\Permission;
use Modules\Authorization\Cache\KeyManager\IKeyManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Cache\Repository as Cache;

class RoleRepository implements IRoleRepository
{
    private IKeyManager $cacheKeyManager;
    private Cache $cache;

    public function __construct(IKeyManager $cacheKeyManager, Cache $cache)
    {
        $this->cacheKeyManager = $cacheKeyManager;
        $this->cache = $cache;
    }
    /**
     * Find role by ID
     *
     * @cache-category Basic Data Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.lookup_ttl') - 1 hour
     * @cache-key authorization:role:id:{id}
     */
    public function findById(string $id): ?Role
    {
        $cacheKey = $this->cacheKeyManager::roleById($id);

        return $this->cache->remember($cacheKey, config('authorization.cache.lookup_ttl'), function () use ($id) {
            return Role::find($id);
        });
    }

    /**
     * Find role by slug
     *
     * @cache-category Basic Data Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.lookup_ttl') - 1 hour
     * @cache-key authorization:role:slug:{slug}
     */
    public function findBySlug(string $slug): ?Role
    {
        $cacheKey = $this->cacheKeyManager::roleBySlug($slug);

        return $this->cache->remember($cacheKey, config('authorization.cache.lookup_ttl'), function () use ($slug) {
            return Role::where('slug', $slug)->first();
        });
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
     *
     * @cache-category Business Logic Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.user_roles_ttl') - 1 hour
     * @cache-key authorization:user:{userId}:roles
     */
    public function getUserRoles(string $userId): Collection
    {
        $cacheKey = $this->cacheKeyManager::userRoles($userId);

        return $this->cache->remember($cacheKey, config('authorization.cache.user_roles_ttl'), function () use ($userId) {
            return Role::whereHas('userRoles', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get();
        });
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