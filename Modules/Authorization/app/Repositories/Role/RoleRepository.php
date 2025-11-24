<?php

namespace Modules\Authorization\Repositories\Role;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Authorization\Models\Role;

/**
 * Role Repository Implementation
 *
 * This class handles all role-related database operations
 * for the authorization module with caching support.
 */
class RoleRepository implements IRoleRepository
{
    /**
     * The Role model instance
     *
     * @var Role
     */
    protected Role $model;

    /**
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * Cache TTL in seconds (30 minutes - reasonable for role data)
     *
     * @var int
     */
    protected int $cacheTtl = 1800;

    /**
     * Constructor
     *
     * @param Role $model The Role model instance
     * @param Cache $cache The cache repository instance
     */
    public function __construct(Role $model, Cache $cache)
    {
        $this->model = $model;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug(string $slug): ?Role
    {
        $cacheKey = "role:slug:{$slug}";

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($slug) {
            return $this->model->where('slug', $slug)->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int $id): ?Role
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
    public function getActive()
    {
        return $this->model->orderBy('name')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Role
    {
        $role = $this->model->create($data);

        // Cache the new role data
        $this->cacheRoleData($role);

        return $role;
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, array $data): bool
    {
        $role = $this->model->find($id);

        if (!$role) {
            return false;
        }

        // Store old slug for cache invalidation
        $oldSlug = $role->slug;

        $result = $role->update($data);

        if ($result) {
            $role->refresh();

            // Invalidate old slug cache if it changed
            if (isset($data['slug']) && $data['slug'] !== $oldSlug && $oldSlug) {
                $this->cache->forget("role:slug:{$oldSlug}");
            }

            // Invalidate and recache role data
            $this->invalidateRoleCaches($id);
            $this->cacheRoleData($role);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): bool
    {
        $role = $this->model->find($id);

        if (!$role) {
            return false;
        }

        $result = $role->delete();

        if ($result) {
            // Invalidate all role caches
            $this->invalidateRoleCaches($id);
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
    public function getPermissions(int $roleId)
    {
        $role = $this->model->find($roleId);

        return $role ? $role->permissions : collect();
    }

    /**
     * {@inheritDoc}
     */
    public function assignPermissions(int $roleId, array $permissionIds): bool
    {
        $role = $this->model->find($roleId);

        if (!$role) {
            return false;
        }

        $role->permissions()->attach($permissionIds);

        // Invalidate role caches
        $this->invalidateRoleCaches($roleId);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function removePermissions(int $roleId, array $permissionIds): bool
    {
        $role = $this->model->find($roleId);

        if (!$role) {
            return false;
        }

        $role->permissions()->detach($permissionIds);

        // Invalidate role caches
        $this->invalidateRoleCaches($roleId);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function syncPermissions(int $roleId, array $permissionIds): bool
    {
        $role = $this->model->find($roleId);

        if (!$role) {
            return false;
        }

        $role->permissions()->sync($permissionIds);

        // Invalidate role caches
        $this->invalidateRoleCaches($roleId);

        return true;
    }

    /**
     * Cache role data in multiple cache keys
     *
     * @param Role $role The role model to cache
     * @return void
     */
    protected function cacheRoleData(Role $role): void
    {
        // Cache by slug (most commonly accessed)
        $this->cache->put("role:slug:{$role->slug}", $role, $this->cacheTtl);
    }

    /**
     * Invalidate all cache keys related to a role
     *
     * @param int $roleId The role ID
     * @return void
     */
    protected function invalidateRoleCaches(int $roleId): void
    {
        // Get role data to know which slug to invalidate
        $role = $this->model->find($roleId);

        if ($role) {
            // Invalidate by slug
            $this->cache->forget("role:slug:{$role->slug}");
        }
    }
}