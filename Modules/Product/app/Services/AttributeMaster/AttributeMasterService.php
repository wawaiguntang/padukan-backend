<?php

namespace Modules\Product\Services\AttributeMaster;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Product\Models\AttributeMaster;
use Modules\Product\Repositories\AttributeMaster\IAttributeMasterRepository;

/**
 * Attribute Master Service Implementation
 *
 * Service for managing global product attributes (color, size, etc.)
 * with proper validation and exception handling.
 */
class AttributeMasterService implements IAttributeMasterService
{
    public function __construct(
        private IAttributeMasterRepository $attributeMasterRepository
    ) {}

    /**
     * Get all attribute masters
     */
    public function getAll(): Collection
    {
        return $this->attributeMasterRepository->getAll();
    }

    /**
     * Get attribute master by ID
     */
    public function getById(string $id): ?AttributeMaster
    {
        return $this->attributeMasterRepository->findById($id);
    }

    /**
     * Get attribute master by key
     */
    public function getByKey(string $key): ?AttributeMaster
    {
        return $this->attributeMasterRepository->findByKey($key);
    }

    /**
     * Create a new attribute master
     */
    public function create(array $data): AttributeMaster
    {
        // Validate attribute data
        $this->validateAttributeData($data);

        // Check key uniqueness
        if ($this->attributeMasterRepository->existsByKey($data['key'])) {
            throw new \InvalidArgumentException(__('exception.duplicate_sku', ['sku' => $data['key']]));
        }

        return DB::transaction(function () use ($data) {
            return $this->attributeMasterRepository->create($data);
        });
    }

    /**
     * Update an existing attribute master
     */
    public function update(string $id, array $data): bool
    {
        // Validate attribute exists
        $attribute = $this->attributeMasterRepository->findById($id);
        if (!$attribute) {
            throw new \RuntimeException(__('exception.category_not_found', ['id' => $id]));
        }

        // Validate update data
        $this->validateAttributeData($data, $id);

        // Check key uniqueness if key is being updated
        if (isset($data['key']) && $this->keyExists($data['key'], $id)) {
            throw new \InvalidArgumentException(__('exception.duplicate_sku', ['sku' => $data['key']]));
        }

        return DB::transaction(function () use ($id, $data) {
            return $this->attributeMasterRepository->update($id, $data);
        });
    }

    /**
     * Delete an attribute master
     */
    public function delete(string $id): bool
    {
        // Validate attribute exists
        $attribute = $this->attributeMasterRepository->findById($id);
        if (!$attribute) {
            throw new \RuntimeException(__('exception.category_not_found', ['id' => $id]));
        }

        // Check if attribute is used in variants (simplified check)
        // In real implementation, check variant relationships

        return DB::transaction(function () use ($id) {
            return $this->attributeMasterRepository->delete($id);
        });
    }

    /**
     * Check if attribute master key exists
     */
    public function keyExists(string $key, ?string $excludeId = null): bool
    {
        return $this->attributeMasterRepository->existsByKey($key, $excludeId);
    }

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    /**
     * Validate attribute master data
     *
     * @param array $data
     * @param string|null $excludeId
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateAttributeData(array $data, ?string $excludeId = null): void
    {
        $errors = [];

        // Required name
        if (empty($data['name'])) {
            $errors[] = __('exception.category.validation_failed');
        }

        // Required key
        if (empty($data['key'])) {
            $errors[] = __('exception.category.validation_failed');
        }

        // Name length
        if (isset($data['name']) && strlen($data['name']) > 255) {
            $errors[] = __('exception.category.validation_failed');
        }

        // Key format validation (alphanumeric, underscore, dash)
        if (isset($data['key']) && !preg_match('/^[a-zA-Z0-9_-]+$/', $data['key'])) {
            $errors[] = __('exception.category.validation_failed');
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(', ', $errors));
        }
    }
}
