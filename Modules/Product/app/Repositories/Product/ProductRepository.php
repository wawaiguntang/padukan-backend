<?php

namespace Modules\Product\Repositories\Product;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Product\Cache\Product\ProductKeyManager;
use Modules\Product\Cache\Product\ProductCacheManager;
use Modules\Product\Cache\Product\ProductTtlManager;
use Modules\Product\Enums\ProductTypeEnum;
use Modules\Product\Models\Product;

/**
 * Product Repository Implementation
 *
 * This class handles all product-related database operations
 * for the product module with caching support.
 */
class ProductRepository implements IProductRepository
{
    /**
     * The Product model instance
     *
     * @var Product
     */
    protected Product $model;

    /**
     * Constructor
     *
     * @param Product $model The Product model instance
     */
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Product
    {
        $cacheKey = ProductKeyManager::productById($id);
        $ttl = ProductTtlManager::productEntity();

        return Cache::remember($cacheKey, $ttl, function () use ($id) {
            return $this->model->find($id);
        });
    }


    /**
     * {@inheritDoc}
     */
    public function getByMerchantId(string $merchantId, bool $includeExpired = false): Collection
    {
        $cacheKey = ProductKeyManager::merchantProducts($merchantId);
        $ttl = ProductTtlManager::productList();

        return Cache::remember($cacheKey, $ttl, function () use ($merchantId, $includeExpired) {
            $query = $this->model->where('merchant_id', $merchantId);

            if (!$includeExpired) {
                $query->where('has_expired', false);
            }

            return $query->orderBy('name')->get();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getByCategoryId(string $categoryId, bool $includeExpired = false): Collection
    {
        $cacheKey = ProductKeyManager::categoryProducts($categoryId);
        $ttl = ProductTtlManager::productList();

        return Cache::remember($cacheKey, $ttl, function () use ($categoryId, $includeExpired) {
            $query = $this->model->where('category_id', $categoryId);

            if (!$includeExpired) {
                $query->where('has_expired', false);
            }

            return $query->orderBy('name')->get();
        });
    }


    /**
     * {@inheritDoc}
     */
    public function create(array $data): Product
    {
        // Generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        // Set default values
        $data['version'] = $data['version'] ?? 1;
        $data['has_variant'] = $data['has_variant'] ?? false;
        $data['has_expired'] = $data['has_expired'] ?? false;

        $product = $this->model->create($data);

        // Invalidate relevant caches using ProductCacheManager
        ProductCacheManager::invalidateForOperation('create', [
            'merchant_id' => $product->merchant_id,
            'category_id' => $product->category_id
        ]);

        return $product;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $product = $this->model->find($id);

        if (!$product) {
            return false;
        }

        // Store old values for cache invalidation
        $oldSlug = $product->slug;
        $oldMerchantId = $product->merchant_id;
        $oldCategoryId = $product->category_id;

        // Handle slug change
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $id);
        }

        // Increment version on update
        if (!isset($data['version'])) {
            $data['version'] = $product->version + 1;
        }

        $result = $product->update($data);

        if ($result) {
            $product->refresh();

            // Invalidate caches using ProductCacheManager
            ProductCacheManager::invalidateForOperation('update', [
                'id' => $id,
                'data' => $data,
                'old_slug' => $oldSlug,
                'old_merchant_id' => $oldMerchantId,
                'old_category_id' => $oldCategoryId
            ]);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $product = $this->model->find($id);

        if (!$product) {
            return false;
        }

        $result = $product->delete();

        if ($result) {
            // Invalidate caches using ProductCacheManager
            ProductCacheManager::invalidateForOperation('delete', [
                'id' => $id,
                'product' => $product->toArray()
            ]);
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
    public function existsSkuForMerchant(string $merchantId, string $sku, ?string $excludeId = null): bool
    {
        $query = $this->model->where('merchant_id', $merchantId)
            ->where('sku', $sku);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function existsBarcodeForMerchant(string $merchantId, string $barcode, ?string $excludeId = null): bool
    {
        $query = $this->model->where('merchant_id', $merchantId)
            ->where('barcode', $barcode);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }


    /**
     * {@inheritDoc}
     */
    public function getProductsWithVariants(string $merchantId): Collection
    {
        return $this->model->where('merchant_id', $merchantId)
            ->where('has_variant', true)
            ->where('has_expired', false)
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getProductsWithoutVariants(string $merchantId): Collection
    {
        return $this->model->where('merchant_id', $merchantId)
            ->where('has_variant', false)
            ->where('has_expired', false)
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiredProducts(string $merchantId): Collection
    {
        return $this->model->where('merchant_id', $merchantId)
            ->where('has_expired', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function updateExpirationStatus(string $id, bool $expired): bool
    {
        return $this->update($id, ['has_expired' => $expired]);
    }


    /**
     * Generate a unique slug for the product
     *
     * @param string $name The product name
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
}
