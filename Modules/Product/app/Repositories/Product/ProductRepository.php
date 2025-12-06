<?php

namespace Modules\Product\Repositories\Product;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Modules\Product\Cache\KeyManager\IKeyManager;
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
     * Cache TTL in seconds (10 minutes - products change frequently)
     *
     * @var int
     */
    protected int $cacheTtl = 600;

    /**
     * Merchant products cache TTL (5 minutes - more volatile)
     *
     * @var int
     */
    protected int $merchantCacheTtl = 300;

    /**
     * Constructor
     *
     * @param Product $model The Product model instance
     * @param Cache $cache The cache repository instance
     * @param IKeyManager $cacheKeyManager The cache key manager instance
     */
    public function __construct(Product $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Product
    {
        $cacheKey = $this->cacheKeyManager::productById($id);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug(string $slug): ?Product
    {
        $cacheKey = $this->cacheKeyManager::productBySlug($slug);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($slug) {
            return $this->model->where('slug', $slug)->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getByMerchantId(string $merchantId, bool $includeExpired = false): Collection
    {
        $cacheKey = $this->cacheKeyManager::merchantProducts($merchantId);

        return $this->cache->remember($cacheKey, $this->merchantCacheTtl, function () use ($merchantId, $includeExpired) {
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
        $cacheKey = $this->cacheKeyManager::categoryProducts($categoryId);

        return $this->cache->remember($cacheKey, $this->merchantCacheTtl, function () use ($categoryId, $includeExpired) {
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
    public function getByType(ProductTypeEnum $type, bool $includeExpired = false): Collection
    {
        $query = $this->model->where('type', $type);

        if (!$includeExpired) {
            $query->where('has_expired', false);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function search(string $query, int $limit = 50): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('name', 'ILIKE', "%{$query}%")
                ->orWhere('description', 'ILIKE', "%{$query}%");
        })
            ->where('has_expired', false)
            ->limit($limit)
            ->orderBy('name')
            ->get();
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

        // Invalidate relevant caches
        $this->invalidateProductCaches($product->merchant_id, $product->category_id);

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

            // Invalidate old caches
            if (isset($data['slug']) && $data['slug'] !== $oldSlug) {
                $this->cache->forget($this->cacheKeyManager::productBySlug($oldSlug));
            }

            // Invalidate merchant and category caches
            $this->invalidateProductCaches(
                $product->merchant_id,
                $product->category_id,
                $oldMerchantId,
                $oldCategoryId
            );
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
            // Invalidate caches
            $this->invalidateProductCaches($product->merchant_id, $product->category_id);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function forceDelete(string $id): bool
    {
        $product = $this->model->withTrashed()->find($id);

        if (!$product) {
            return false;
        }

        $result = $product->forceDelete();

        if ($result) {
            // Invalidate caches
            $this->invalidateProductCaches($product->merchant_id, $product->category_id);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function restore(string $id): bool
    {
        $product = $this->model->withTrashed()->find($id);

        if (!$product) {
            return false;
        }

        $result = $product->restore();

        if ($result) {
            // Invalidate caches
            $this->invalidateProductCaches($product->merchant_id, $product->category_id);
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
    public function incrementVersion(string $id): bool
    {
        $product = $this->model->find($id);

        if (!$product) {
            return false;
        }

        return $product->increment('version');
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
     * {@inheritDoc}
     */
    public function getProductWithRelations(string $id): ?Product
    {
        return $this->model->with(['category', 'variants', 'extras', 'serviceDetails'])
            ->find($id);
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

    /**
     * Invalidate product-related caches
     *
     * @param string|null $merchantId Current merchant ID
     * @param string|null $categoryId Current category ID
     * @param string|null $oldMerchantId Old merchant ID (for updates)
     * @param string|null $oldCategoryId Old category ID (for updates)
     * @return void
     */
    protected function invalidateProductCaches(
        ?string $merchantId = null,
        ?string $categoryId = null,
        ?string $oldMerchantId = null,
        ?string $oldCategoryId = null
    ): void {
        // Invalidate merchant caches
        if ($merchantId) {
            $this->cache->forget($this->cacheKeyManager::merchantProducts($merchantId));
        }
        if ($oldMerchantId && $oldMerchantId !== $merchantId) {
            $this->cache->forget($this->cacheKeyManager::merchantProducts($oldMerchantId));
        }

        // Invalidate category caches
        if ($categoryId) {
            $this->cache->forget($this->cacheKeyManager::categoryProducts($categoryId));
        }
        if ($oldCategoryId && $oldCategoryId !== $categoryId) {
            $this->cache->forget($this->cacheKeyManager::categoryProducts($oldCategoryId));
        }
    }
}
