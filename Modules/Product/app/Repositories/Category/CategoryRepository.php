<?php

namespace Modules\Product\Repositories\Category;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Category;

/**
 * Simple Category Repository
 *
 * Ultra-minimal repository using Eloquent directly.
 * Implements ICategoryRepository interface for consistency.
 */
class CategoryRepository implements ICategoryRepository
{
    /**
     * Find category by ID
     */
    public function find(string $id): ?Category
    {
        return Category::find($id);
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    /**
     * Get all categories
     */
    public function all(): Collection
    {
        return Category::all();
    }

    /**
     * Create new category
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update category
     */
    public function update(string $id, array $data): bool
    {
        return Category::where('id', $id)->update($data);
    }

    /**
     * Delete category
     */
    public function delete(string $id): bool
    {
        $category = Category::find($id);

        if (!$category) {
            return false;
        }

        return $category->delete();
    }

    /**
     * Get root categories
     */
    public function getRoots(): Collection
    {
        return Category::whereNull('parent_id')->get();
    }

    /**
     * Get children of a category
     */
    public function getChildren(string $parentId): Collection
    {
        return Category::where('parent_id', $parentId)->get();
    }
}
