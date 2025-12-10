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
    public function find(string $id): ?Category;

    /**
     * Find category by slug
     *
     * @param string $slug
     * @return Category|null
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Get all categories
     *
     * @return Collection The collection of all categories
     */
    public function all(): Collection;

    /**
     * Create a new category
     *
     * @param array $data Category data containing:
     * - name: string - Category name
     * - description?: string - Category description (optional)
     * - parent_id?: string - Parent category ID (optional)
     * @return Category The created category model
     */
    public function create(array $data): Category;

    /**
     * Update an existing category
     *
     * @param string $id The category's UUID
     * @param array $data Category data to update (same structure as create)
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
     * Get root categories (categories without parent)
     *
     * @return Collection The collection of root categories
     */
    public function getRoots(): Collection;

    /**
     * Get child categories of a parent category
     *
     * @param string $parentId The parent category ID
     * @return Collection The collection of child categories
     */
    public function getChildren(string $parentId): Collection;
}
