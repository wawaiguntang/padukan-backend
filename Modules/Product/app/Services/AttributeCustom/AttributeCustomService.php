<?php

namespace Modules\Product\Services\AttributeCustom;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Product\Models\AttributeCustom;
use Modules\Product\Repositories\AttributeCustom\IAttributeCustomRepository;

/**
 * Attribute Custom Service Implementation
 *
 * Service for managing merchant-specific product attributes
 * with proper validation and exception handling.
 */
class AttributeCustomService implements IAttributeCustomService
{
    public function __construct(
        private IAttributeCustomRepository $attributeCustomRepository
    ) {}

    /**
     * Get all attribute customs for a merchant
     */
    public function getAllByMerchant(string $merchantId): Collection
    {
        return $this->attributeCustomRepository->getByMerchantId($merchantId);
    }

    /**
     * Get attribute custom by ID
     */
    public function getById(string $id, string $merchantId): ?AttributeCustom
    {
        $attribute = $this->attributeCustomRepository->findById($id);
        return $attribute && $attribute->merchant_id === $merchantId ? $attribute : null;
    }

    /**
     * Get attribute custom by key for a merchant
     */
    public function getByKey(string $key, string $merchantId): ?AttributeCustom
    {
        // Since the repository doesn't have findByKeyAndMerchant, we'll get all by merchant and filter
        $attributes = $this->attributeCustomRepository->getByMerchantId($merchantId);
        return $attributes->firstWhere('key', $key);
    }

    /**
     * Create a new attribute custom for a merchant
     */
    public function create(array $data, string $merchantId): AttributeCustom
    {
        // Validate attribute data
        $this->validateAttributeData($data);

        // Check key uniqueness for this merchant
        if ($this->attributeCustomRepository->existsForMerchant($merchantId, $data['key'])) {
            throw new \InvalidArgumentException(__('exception.duplicate_sku', ['sku' => $data['key']]));
        }

        $data['merchant_id'] = $merchantId;

        return DB::transaction(function () use ($data) {
            return $this->attributeCustomRepository->create($data);
        });
    }

    /**
     * Update an existing attribute custom
     */
    public function update(string $id, array $data, string $merchantId): bool
    {
        // Validate attribute exists and belongs to merchant
        $attribute = $this->attributeCustomRepository->findById($id);
        if (!$attribute || $attribute->merchant_id !== $merchantId) {
            throw new \RuntimeException(__('exception.category_not_found', ['id' => $id]));
        }

        // Validate update data
        $this->validateAttributeData($data, $id);

        // Check key uniqueness if key is being updated
        if (isset($data['key']) && $this->keyExists($data['key'], $merchantId, $id)) {
            throw new \InvalidArgumentException(__('exception.duplicate_sku', ['sku' => $data['key']]));
        }

        return DB::transaction(function () use ($id, $data) {
            return $this->attributeCustomRepository->update($id, $data);
        });
    }

    /**
     * Delete an attribute custom
     */
    public function delete(string $id, string $merchantId): bool
    {
        // Validate attribute exists and belongs to merchant
        $attribute = $this->attributeCustomRepository->findById($id);
        if (!$attribute || $attribute->merchant_id !== $merchantId) {
            return false;
        }

        // Check if attribute is used in variants (simplified check)
        // In real implementation, check variant relationships

        return DB::transaction(function () use ($id) {
            return $this->attributeCustomRepository->delete($id);
        });
    }

    /**
     * Check if attribute custom key exists for a merchant
     */
    public function keyExists(string $key, string $merchantId, ?string $excludeId = null): bool
    {
        return $this->attributeCustomRepository->existsForMerchant($merchantId, $key, $excludeId);
    }

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    /**
     * Validate attribute custom data
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
