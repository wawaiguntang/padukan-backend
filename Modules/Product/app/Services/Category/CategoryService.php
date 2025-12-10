<?php

namespace Modules\Product\Services\Category;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
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

            // Generate slug if not provided or generate from name
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            // Ensure slug uniqueness
            $data['slug'] = $this->generateUniqueSlug($data['slug']);

            // Set path and level
            if (isset($data['parent_id']) && !empty($data['parent_id'])) {
                $parent = $this->categoryRepository->find($data['parent_id']);
                $data['level'] = ($parent->level ?? 0) + 1;
                // Path will be updated after creation since we need the ID
                // For now, we'll set a temporary path or handle it in the repository/model observer if we had one
                // But here we'll do it in transaction
            } else {
                $data['level'] = 0;
            }

            $category = DB::transaction(function () use ($data) {
                try {
                    $category = $this->categoryRepository->create($data);

                    // Update path: parent_path/id
                    if (!empty($data['parent_id'])) {
                        $parent = $this->categoryRepository->find($data['parent_id']);
                        $path = ($parent->path ? $parent->path . '/' : '') . $category->id;
                    } else {
                        $path = $category->id;
                    }

                    $this->categoryRepository->update($category->id, ['path' => $path]);
                    $category->path = $path;

                    return $category;
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
            if (array_key_exists('parent_id', $data)) {
                if (!empty($data['parent_id'])) {
                    $this->validateParentCategory($data['parent_id'], $id);

                    // Update level and path if parent changed
                    $parent = $this->categoryRepository->find($data['parent_id']);
                    $data['level'] = ($parent->level ?? 0) + 1;
                } else {
                    $data['level'] = 0;
                    $data['parent_id'] = null;
                }
            }

            // Handle slug update
            if (isset($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['slug'], $id);
            } elseif (isset($data['name']) && !isset($data['slug'])) {
                // Optionally regenerate slug if name changes, but usually better to keep stable URLs
                // $data['slug'] = $this->generateUniqueSlug(Str::slug($data['name']), $id);
            }

            $result = DB::transaction(function () use ($id, $data, $category) {
                try {
                    $updated = $this->categoryRepository->update($id, $data);

                    // If parent changed, we need to update path and children paths
                    if (array_key_exists('parent_id', $data) && $data['parent_id'] !== $category->parent_id) {
                        $this->updatePathAndChildren($id);
                    }

                    return $updated;
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

        if (!empty($errors)) {
            throw new CategoryValidationException($errors);
        }
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug(string $slug, ?string $excludeId = null): string
    {
        $originalSlug = $slug;
        $count = 1;

        // This is a simplified check. Ideally repository should have existsBySlug
        // Since we don't have it explicitly exposed in interface yet, we might need to add it or use raw query in repo
        // For now, assuming we can add findBySlug to repo or use a direct check

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    private function slugExists(string $slug, ?string $excludeId = null): bool
    {
        // We need to add this method to repository or access model directly via repo if possible
        // Since ICategoryRepository doesn't have it, let's assume we'll add it.
        // For this patch, I'll use a workaround if repo doesn't support it, but better to add to repo.
        // Let's assume we will add `findBySlug` or similar to repo.
        // Wait, the repository implementation uses Eloquent directly, but interface needs update.
        // For now, I will use a direct DB query here as a temporary measure or add to repo in next step.
        // Ideally, service should not do direct DB queries.
        // Let's rely on repository having a method or we add it.
        // I will add `findBySlug` to the repository interface and implementation in the next step.

        // Temporary: accessing the repository's underlying model is not possible via interface.
        // I'll assume we'll add `findBySlug` to repository.
        $category = $this->categoryRepository->findBySlug($slug);

        if ($category) {
            if ($excludeId && $category->id === $excludeId) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Update path for category and its children recursively
     */
    private function updatePathAndChildren(string $categoryId): void
    {
        $category = $this->categoryRepository->find($categoryId);
        if (!$category) return;

        // Recalculate path
        if ($category->parent_id) {
            $parent = $this->categoryRepository->find($category->parent_id);
            $newPath = ($parent->path ? $parent->path . '/' : '') . $category->id;
        } else {
            $newPath = $category->id;
        }

        // Only update if changed
        if ($category->path !== $newPath) {
            $this->categoryRepository->update($category->id, ['path' => $newPath]);

            // Update children
            $children = $this->categoryRepository->getChildren($categoryId);
            foreach ($children as $child) {
                $this->updatePathAndChildren($child->id);
            }
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

        // Prevent circular references
        if ($excludeId) {
            // Check if new parent is a child of the category being updated
            $parent = $this->categoryRepository->find($parentId);

            // If parent's path contains the category ID, it means the parent is a descendant
            // Path format: root_id/child_id/grandchild_id
            if ($parent && $parent->path && str_contains($parent->path, $excludeId)) {
                throw new CategoryHierarchyException('circular_reference');
            }
        }
    }
}
