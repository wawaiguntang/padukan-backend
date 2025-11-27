<?php

namespace Modules\Authorization\Repositories\Permission;

use Modules\Authorization\Models\Permission;
use Modules\Authorization\Models\UserRole;
use Modules\Authorization\Models\RolePermission;
use Modules\Authorization\Cache\KeyManager\IKeyManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Cache\Repository as Cache;

class PermissionRepository implements IPermissionRepository
{
    private IKeyManager $cacheKeyManager;
    private Cache $cache;

    public function __construct(IKeyManager $cacheKeyManager, Cache $cache)
    {
        $this->cacheKeyManager = $cacheKeyManager;
        $this->cache = $cache;
    }
    /**
     * Find permission by ID
     *
     * @cache-category Basic Data Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.lookup_ttl') - 1 hour
     * @cache-key authorization:permission:id:{id}
     */
    public function findById(string $id): ?Permission
    {
        $cacheKey = $this->cacheKeyManager::permissionById($id);

        return $this->cache->remember($cacheKey, config('authorization.cache.lookup_ttl'), function () use ($id) {
            return Permission::find($id);
        });
    }

    /**
     * Find permission by slug
     *
     * @cache-category Basic Data Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.lookup_ttl') - 1 hour
     * @cache-key authorization:permission:slug:{slug}
     */
    public function findBySlug(string $slug): ?Permission
    {
        $cacheKey = $this->cacheKeyManager::permissionBySlug($slug);

        return $this->cache->remember($cacheKey, config('authorization.cache.lookup_ttl'), function () use ($slug) {
            return Permission::where('slug', $slug)->first();
        });
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
     *
     * @cache-category Business Logic Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.user_permissions_ttl') - 1 hour
     * @cache-key authorization:user:{userId}:permissions
     */
    public function getUserPermissions(string $userId): Collection
    {
        $cacheKey = $this->cacheKeyManager::userPermissions($userId);

        return $this->cache->remember($cacheKey, config('authorization.cache.user_permissions_ttl'), function () use ($userId) {
            return Permission::whereHas('rolePermissions', function ($query) use ($userId) {
                $query->whereHas('role', function ($subQuery) use ($userId) {
                    $subQuery->whereHas('userRoles', function ($subSubQuery) use ($userId) {
                        $subSubQuery->where('user_id', $userId);
                    });
                });
            })->get();
        });
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
     *
     * @cache-category Business Logic Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.role_permissions_ttl') - 1 hour
     * @cache-key authorization:role:{roleId}:permissions
     */
    public function getRolePermissions(string $roleId): Collection
    {
        $cacheKey = $this->cacheKeyManager::rolePermissions($roleId);

        return $this->cache->remember($cacheKey, config('authorization.cache.role_permissions_ttl'), function () use ($roleId) {
            return Permission::whereHas('rolePermissions', function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })->get();
        });
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