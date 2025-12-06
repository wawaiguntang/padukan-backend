<?php

namespace Modules\Product\Repositories\Category;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Category;

/**
 * Interface for Category Repository
 *
 * This interface defines the contract for category data operations
 * in the product module.
 */
interface ICategoryRepository
{
    /**
     * Find a category by its ID
     *
     * @param string $id The category's UUID
     * @return Category|null The category model if found, null otherwise
     */
    public function findById(string $id): ?Category;

    /**
     * Find a category by its slug
     *
     * @param string $slug The category's slug
     * @return Category|null The category model if found, null otherwise
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Get all root categories (categories without parent)
     *
     * @return Collection The collection of root categories
     */
    public function getRootCategories(): Collection;

    /**
     * Get child categories of a parent category
     *
     * @param string $parentId The parent category ID
     * @return Collection The collection of child categories
     */
    public function getChildCategories(string $parentId): Collection;

    /**
     * Get category hierarchy (parent and children)
     *
     * @param string $categoryId The category ID
     * @return Category|null The category with loaded relationships
     */
    public function getCategoryHierarchy(string $categoryId): ?Category;

    /**
     * Create a new category
     *
     * @param array $data Category data containing:
     * - name: string - Category name
     * - slug?: string - Category slug (auto-generated if not provided)
     * - description?: string - Category description
     * - parent_id?: string - Parent category ID
     * - metadata?: array - Additional metadata
     * @return Category The created category model
     */
    public function create(array $data): Category;

    /**
     * Update an existing category
     *
     * @param string $id The category's UUID
     * @param array $data Category data to update
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete a category
     *
     * @param string $id The category's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;

    /**
     * Check if a category exists by slug
     *
     * @param string $slug The category slug
     * @param string|null $excludeId Exclude this ID from check (for updates)
     * @return bool True if category exists, false otherwise
     */
    public function existsBySlug(string $slug, ?string $excludeId = null): bool;

    /**
     * Get categories by level
     *
     * @param int $level The category level
     * @return Collection The collection of categories at the specified level
     */
    public function getByLevel(int $level): Collection;

    /**
     * Get category path (breadcrumb)
     *
     * @param string $categoryId The category ID
     * @return Collection The collection of categories in the path
     */
    public function getCategoryPath(string $categoryId): Collection;

    /**
     * Move category to new parent
     *
     * @param string $categoryId The category ID
     * @param string|null $newParentId The new parent ID (null for root)
     * @return bool True if move was successful, false otherwise
     */
    public function moveCategory(string $categoryId, ?string $newParentId): bool;

    /**
     * Update category path and level based on parent
     *
     * @param string $categoryId The category ID
     * @return bool True if update was successful, false otherwise
     */
    public function updateCategoryPath(string $categoryId): bool;
}
