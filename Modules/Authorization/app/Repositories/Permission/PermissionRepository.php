<?php

namespace Modules\Authorization\Repositories\Permission;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Authorization\Models\Permission;

/**
 * Permission Repository Implementation
 *
 * This class handles all permission-related database operations
 * for the authorization module with caching support.
 */
class PermissionRepository implements IPermissionRepository
{
    /**
     * The Permission model instance
     *
     * @var Permission
     */
    protected Permission $model;

    /**
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * Cache TTL in seconds (30 minutes - reasonable for permission data)
     *
     * @var int
     */
    protected int $cacheTtl = 1800;

    /**
     * Constructor
     *
     * @param Permission $model The Permission model instance
     * @param Cache $cache The cache repository instance
     */
    public function __construct(Permission $model, Cache $cache)
    {
        $this->model = $model;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug(string $slug): ?Permission
    {
        $cacheKey = "permission:slug:{$slug}";

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($slug) {
            return $this->model->where('slug', $slug)->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int $id): ?Permission
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll()
    {
        return $this->model->orderBy('name')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getByModule(string $module)
    {
        return $this->model->where('module', $module)->orderBy('name')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Permission
    {
        $permission = $this->model->create($data);

        // Cache the new permission data
        $this->cachePermissionData($permission);

        return $permission;
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, array $data): bool
    {
        $permission = $this->model->find($id);

        if (!$permission) {
            return false;
        }

        // Store old slug for cache invalidation
        $oldSlug = $permission->slug;

        $result = $permission->update($data);

        if ($result) {
            $permission->refresh();

            // Invalidate old slug cache if it changed
            if (isset($data['slug']) && $data['slug'] !== $oldSlug && $oldSlug) {
                $this->cache->forget("permission:slug:{$oldSlug}");
            }

            // Invalidate and recache permission data
            $this->invalidatePermissionCaches($id);
            $this->cachePermissionData($permission);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): bool
    {
        $permission = $this->model->find($id);

        if (!$permission) {
            return false;
        }

        $result = $permission->delete();

        if ($result) {
            // Invalidate all permission caches
            $this->invalidatePermissionCaches($id);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function existsBySlug(string $slug): bool
    {
        return $this->model->where('slug', $slug)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(int $permissionId)
    {
        $permission = $this->model->find($permissionId);

        return $permission ? $permission->roles : collect();
    }

    /**
     * {@inheritDoc}
     */
    public function search(string $query)
    {
        return $this->model
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('slug', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->get();
    }

    /**
     * Cache permission data in multiple cache keys
     *
     * @param Permission $permission The permission model to cache
     * @return void
     */
    protected function cachePermissionData(Permission $permission): void
    {
        // Cache by slug (most commonly accessed)
        $this->cache->put("permission:slug:{$permission->slug}", $permission, $this->cacheTtl);
    }

    /**
     * Invalidate all cache keys related to a permission
     *
     * @param int $permissionId The permission ID
     * @return void
     */
    protected function invalidatePermissionCaches(int $permissionId): void
    {
        // Get permission data to know which slug to invalidate
        $permission = $this->model->find($permissionId);

        if ($permission) {
            // Invalidate by slug
            $this->cache->forget("permission:slug:{$permission->slug}");
        }
    }
}