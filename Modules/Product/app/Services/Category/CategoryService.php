<?php

namespace Modules\Product\Services\Category;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Category;
use Modules\Product\Repositories\Category\CategoryRepository;

/**
 * Simple Category Service
 *
 * Ultra-minimal service for basic category operations.
 * Implements ICategoryService interface for consistency.
 */
class CategoryService implements ICategoryService
{
    /**
     * Category repository
     */
    protected CategoryRepository $repository;

    /**
     * Constructor
     */
    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all categories
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get category by ID
     */
    public function getById(string $id): ?Category
    {
        return $this->repository->find($id);
    }

    /**
     * Create category
     */
    public function create(array $data): Category
    {
        return $this->repository->create($data);
    }

    /**
     * Update category
     */
    public function update(string $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete category
     */
    public function delete(string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get root categories
     */
    public function getRoots(): Collection
    {
        return $this->repository->getRoots();
    }

    /**
     * Get category children
     */
    public function getChildren(string $parentId): Collection
    {
        return $this->repository->getChildren($parentId);
    }
}
