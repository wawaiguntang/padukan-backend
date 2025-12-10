<?php

namespace Modules\Product\Repositories\Category;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Product\Models\Category;
use Modules\Product\Cache\Category\CategoryKeyManager;
use Modules\Product\Cache\Category\CategoryCacheManager;
use Modules\Product\Cache\Category\CategoryTtlManager;

/**
 * Simple Category Repository
 *
 * Ultra-minimal repository using Eloquent directly.
 * Implements ICategoryRepository interface for consistency.
 * Uses KeyManager and TtlManager for cache operations.
 */
class CategoryRepository implements ICategoryRepository
{
    /**
     * Find category by ID with caching
     */
    public function find(string $id): ?Category
    {
        $cacheKey = CategoryKeyManager::categoryById($id);
        $ttl = CategoryTtlManager::categoryEntity();

        return Cache::remember($cacheKey, $ttl, function () use ($id) {
            return Category::find($id);
        });
    }

    /**
     * Find category by slug with caching
     */
    public function findBySlug(string $slug): ?Category
    {
        $cacheKey = CategoryKeyManager::categoryBySlug($slug);
        $ttl = CategoryTtlManager::categoryEntity();

        return Cache::remember($cacheKey, $ttl, function () use ($slug) {
            return Category::where('slug', $slug)->first();
        });
    }

    /**
     * Get all categories with caching
     */
    public function all(): Collection
    {
        $cacheKey = CategoryKeyManager::allCategories();
        $ttl = CategoryTtlManager::categoryList();

        return Cache::remember($cacheKey, $ttl, function () {
            return Category::all();
        });
    }

    /**
     * Create new category and invalidate relevant caches
     */
    public function create(array $data): Category
    {
        $category = Category::create($data);

        // Smart invalidation using CategoryCacheManager
        CategoryCacheManager::invalidateForOperation('create');

        return $category;
    }

    /**
     * Update category and invalidate relevant caches
     */
    public function update(string $id, array $data): bool
    {
        $result = Category::where('id', $id)->update($data);

        if ($result) {
            // Smart invalidation using CategoryCacheManager
            CategoryCacheManager::invalidateForOperation('update', [
                'id' => $id,
                'data' => $data
            ]);
        }

        return $result;
    }

    /**
     * Delete category and invalidate relevant caches
     */
    public function delete(string $id): bool
    {
        $category = Category::find($id);

        if (!$category) {
            return false;
        }

        $result = $category->delete();

        if ($result) {
            // Smart invalidation using CategoryCacheManager
            CategoryCacheManager::invalidateForOperation('delete', [
                'id' => $id,
                'category' => $category->toArray()
            ]);
        }

        return $result;
    }

    /**
     * Get root categories with caching
     */
    public function getRoots(): Collection
    {
        $cacheKey = CategoryKeyManager::categoryRoots();
        $ttl = CategoryTtlManager::categoryList();

        return Cache::remember($cacheKey, $ttl, function () {
            return Category::whereNull('parent_id')->get();
        });
    }

    /**
     * Get children of a category with caching
     */
    public function getChildren(string $parentId): Collection
    {
        $cacheKey = CategoryKeyManager::categoryChildren($parentId);
        $ttl = CategoryTtlManager::categoryList();

        return Cache::remember($cacheKey, $ttl, function () use ($parentId) {
            return Category::where('parent_id', $parentId)->get();
        });
    }
}
