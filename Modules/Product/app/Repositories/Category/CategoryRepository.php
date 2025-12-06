<?php

namespace Modules\Product\Repositories\Category;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Modules\Product\Cache\KeyManager\IKeyManager;
use Modules\Product\Models\Category;

/**
 * Category Repository Implementation
 *
 * This class handles all category-related database operations
 * for the product module with caching support.
 */
class CategoryRepository implements ICategoryRepository
{
    /**
     * The Category model instance
     *
     * @var Category
     */
    protected Category $model;

    /**
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * The cache key manager instance
     *
     * @var IKeyManager
     */
    protected IKeyManager $cacheKeyManager;

    /**
     * Cache TTL in seconds (15 minutes - reasonable for category data)
     *
     * @var int
     */
    protected int $cacheTtl = 900;

    /**
     * Root categories cache TTL (30 minutes)
     *
     * @var int
     */
    protected int $rootCacheTtl = 1800;

    /**
     * Constructor
     *
     * @param Category $model The Category model instance
     * @param Cache $cache The cache repository instance
     * @param IKeyManager $cacheKeyManager The cache key manager instance
     */
    public function __construct(Category $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Category
    {
        $cacheKey = $this->cacheKeyManager::categoryById($id);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug(string $slug): ?Category
    {
        $cacheKey = $this->cacheKeyManager::categoryBySlug($slug);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($slug) {
            return $this->model->where('slug', $slug)->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getRootCategories(): Collection
    {
        $cacheKey = $this->cacheKeyManager::rootCategories();

        return $this->cache->remember($cacheKey, $this->rootCacheTtl, function () {
            return $this->model->whereNull('parent_id')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getChildCategories(string $parentId): Collection
    {
        return $this->model->where('parent_id', $parentId)
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getCategoryHierarchy(string $categoryId): ?Category
    {
        $category = $this->findById($categoryId);

        if ($category) {
            $category->load(['parent', 'children']);
        }

        return $category;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Category
    {
        // Generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        // Set level and path based on parent
        if (isset($data['parent_id']) && $data['parent_id']) {
            $parent = $this->findById($data['parent_id']);
            if ($parent) {
                $data['level'] = $parent->level + 1;
                $data['path'] = $parent->path ? $parent->path . '/' . $data['slug'] : $data['slug'];
            }
        } else {
            $data['level'] = 1;
            $data['path'] = $data['slug'];
        }

        $category = $this->model->create($data);

        // Invalidate relevant caches
        $this->invalidateCategoryCaches();

        return $category;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $category = $this->model->find($id);

        if (!$category) {
            return false;
        }

        // Store old values for cache invalidation
        $oldSlug = $category->slug;
        $oldParentId = $category->parent_id;

        // Handle slug change
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $id);
        }

        // Handle parent change - update level and path
        if (isset($data['parent_id']) && $data['parent_id'] !== $oldParentId) {
            if ($data['parent_id']) {
                $parent = $this->findById($data['parent_id']);
                if ($parent) {
                    $data['level'] = $parent->level + 1;
                    $data['path'] = $parent->path ? $parent->path . '/' . ($data['slug'] ?? $category->slug) : ($data['slug'] ?? $category->slug);
                }
            } else {
                $data['level'] = 1;
                $data['path'] = $data['slug'] ?? $category->slug;
            }

            // Update children paths if parent changed
            $this->updateChildrenPaths($id, $data['path'] ?? $category->path);
        }

        $result = $category->update($data);

        if ($result) {
            $category->refresh();

            // Invalidate old caches
            if (isset($data['slug']) && $data['slug'] !== $oldSlug) {
                $this->cache->forget($this->cacheKeyManager::categoryBySlug($oldSlug));
            }

            // Invalidate all category caches since hierarchy might have changed
            $this->invalidateCategoryCaches();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $category = $this->model->find($id);

        if (!$category) {
            return false;
        }

        // Check if category has children
        if ($category->children()->exists()) {
            return false; // Cannot delete category with children
        }

        $result = $category->delete();

        if ($result) {
            // Invalidate caches
            $this->invalidateCategoryCaches();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function existsBySlug(string $slug, ?string $excludeId = null): bool
    {
        $query = $this->model->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function getByLevel(int $level): Collection
    {
        return $this->model->where('level', $level)
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getCategoryPath(string $categoryId): Collection
    {
        $category = $this->findById($categoryId);

        if (!$category) {
            return collect();
        }

        $path = collect([$category]);
        $current = $category;

        while ($current->parent) {
            $path->prepend($current->parent);
            $current = $current->parent;
        }

        return $path;
    }

    /**
     * {@inheritDoc}
     */
    public function moveCategory(string $categoryId, ?string $newParentId): bool
    {
        return $this->update($categoryId, ['parent_id' => $newParentId]);
    }

    /**
     * {@inheritDoc}
     */
    public function updateCategoryPath(string $categoryId): bool
    {
        $category = $this->model->find($categoryId);

        if (!$category) {
            return false;
        }

        if ($category->parent_id) {
            $parent = $this->findById($category->parent_id);
            if ($parent) {
                $category->level = $parent->level + 1;
                $category->path = $parent->path ? $parent->path . '/' . $category->slug : $category->slug;
            }
        } else {
            $category->level = 1;
            $category->path = $category->slug;
        }

        return $category->save();
    }

    /**
     * Generate a unique slug for the category
     *
     * @param string $name The category name
     * @param string|null $excludeId Exclude this ID from uniqueness check
     * @return string The unique slug
     */
    protected function generateUniqueSlug(string $name, ?string $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->existsBySlug($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Update children paths when parent path changes
     *
     * @param string $parentId The parent category ID
     * @param string $newParentPath The new parent path
     * @return void
     */
    protected function updateChildrenPaths(string $parentId, string $newParentPath): void
    {
        $children = $this->model->where('parent_id', $parentId)->get();

        foreach ($children as $child) {
            $child->path = $newParentPath . '/' . $child->slug;
            $child->level = substr_count($child->path, '/') + 1;
            $child->save();

            // Recursively update grandchildren
            $this->updateChildrenPaths($child->id, $child->path);
        }
    }

    /**
     * Invalidate all category-related caches
     *
     * @return void
     */
    protected function invalidateCategoryCaches(): void
    {
        // Clear root categories cache
        $this->cache->forget($this->cacheKeyManager::rootCategories());

        // Clear all category caches (this is broad but ensures consistency)
        // In a production system, you might want to be more selective
        $this->cache->forget($this->cacheKeyManager::categoryPattern());
    }
}
