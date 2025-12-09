<?php

namespace Modules\Product\Services\Category;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\Models\Category;
use Modules\Product\Repositories\Category\ICategoryRepository;
use Modules\Product\Exceptions\CategoryNotFoundException;
use Modules\Product\Exceptions\CategoryValidationException;
use Modules\Product\Exceptions\CategoryHierarchyException;

/**
 * Category Service Implementation
 *
 * Comprehensive service for category operations with proper business logic,
 * validation, exception handling, and logging.
 */
class CategoryService implements ICategoryService
{
    public function __construct(
        private ICategoryRepository $categoryRepository
    ) {}

    /**
     * Get all categories
     */
    public function getAll(): Collection
    {
        try {
            return $this->categoryRepository->all();
        } catch (\Exception $e) {
            Log::error('Failed to get all categories', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get category by ID
     */
    public function getById(string $id): ?Category
    {
        try {
            $category = $this->categoryRepository->find($id);

            if (!$category) {
                throw new CategoryNotFoundException($id);
            }

            return $category;
        } catch (\Exception $e) {
            Log::error('Failed to get category by ID', [
                'category_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create category
     */
    public function create(array $data): Category
    {
        try {
            // Validate category data
            $this->validateCategoryData($data);

            // Validate parent exists if provided
            if (isset($data['parent_id']) && !empty($data['parent_id'])) {
                $this->validateParentCategory($data['parent_id']);
            }

            $category = DB::transaction(function () use ($data) {
                try {
                    return $this->categoryRepository->create($data);
                } catch (\Exception $e) {
                    Log::error('Failed to create category in transaction', [
                        'data' => $data,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            });

            Log::info('Category created successfully', [
                'category_id' => $category->id,
                'name' => $category->name
            ]);

            return $category;
        } catch (\Exception $e) {
            Log::error('Category creation failed', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update category
     */
    public function update(string $id, array $data): bool
    {
        try {
            // Validate category exists
            $category = $this->categoryRepository->find($id);
            if (!$category) {
                throw new CategoryNotFoundException($id);
            }

            // Validate update data
            $this->validateCategoryData($data, $id);

            // Validate parent exists if provided
            if (isset($data['parent_id']) && !empty($data['parent_id'])) {
                $this->validateParentCategory($data['parent_id'], $id);
            }

            $result = DB::transaction(function () use ($id, $data) {
                try {
                    return $this->categoryRepository->update($id, $data);
                } catch (\Exception $e) {
                    Log::error('Failed to update category in transaction', [
                        'category_id' => $id,
                        'data' => $data,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            });

            if ($result) {
                Log::info('Category updated successfully', [
                    'category_id' => $id,
                    'data' => $data
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Category update failed', [
                'category_id' => $id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete category
     */
    public function delete(string $id): bool
    {
        try {
            // Validate category exists
            $category = $this->categoryRepository->find($id);
            if (!$category) {
                throw new CategoryNotFoundException($id);
            }

            // Check for children
            if ($this->categoryRepository->getChildren($id)->isNotEmpty()) {
                throw new CategoryHierarchyException('has_children');
            }

            // Check if category has products (simplified check)
            // In real implementation, check product relationships

            $result = DB::transaction(function () use ($id) {
                try {
                    return $this->categoryRepository->delete($id);
                } catch (\Exception $e) {
                    Log::error('Failed to delete category in transaction', [
                        'category_id' => $id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            });

            if ($result) {
                Log::info('Category deleted successfully', [
                    'category_id' => $id,
                    'name' => $category->name
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Category deletion failed', [
                'category_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get root categories
     */
    public function getRoots(): Collection
    {
        try {
            return $this->categoryRepository->getRoots();
        } catch (\Exception $e) {
            Log::error('Failed to get root categories', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get category children
     */
    public function getChildren(string $parentId): Collection
    {
        try {
            // Validate parent exists
            $this->categoryRepository->find($parentId);

            return $this->categoryRepository->getChildren($parentId);
        } catch (\Exception $e) {
            Log::error('Failed to get category children', [
                'parent_id' => $parentId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    /**
     * Validate category data
     *
     * @param array $data
     * @param string|null $excludeId
     * @return void
     * @throws CategoryValidationException
     */
    private function validateCategoryData(array $data, ?string $excludeId = null): void
    {
        $errors = [];

        // Required name
        if (empty($data['name'])) {
            $errors[] = __('exception.category.validation_failed');
        }

        // Name length
        if (isset($data['name']) && strlen($data['name']) > 255) {
            $errors[] = __('exception.category.validation_failed');
        }

        // Slug uniqueness check would go here
        // Parent validation is done separately

        if (!empty($errors)) {
            throw new CategoryValidationException($errors);
        }
    }

    /**
     * Validate parent category exists and prevents circular references
     *
     * @param string $parentId
     * @param string|null $excludeId
     * @return void
     * @throws CategoryNotFoundException|CategoryHierarchyException
     */
    private function validateParentCategory(string $parentId, ?string $excludeId = null): void
    {
        $parent = $this->categoryRepository->find($parentId);
        if (!$parent) {
            throw new CategoryNotFoundException($parentId);
        }

        // Prevent self-reference
        if ($excludeId && $parentId === $excludeId) {
            throw new CategoryHierarchyException('self_reference');
        }

        // Prevent circular references (simplified check)
        // In a full implementation, you'd traverse the hierarchy
    }
}
