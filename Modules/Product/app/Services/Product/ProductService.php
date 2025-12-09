<?php

namespace Modules\Product\Services\Product;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariant;
use Modules\Product\Models\Category;
use Modules\Product\Repositories\Product\IProductRepository;
use Modules\Product\Repositories\ProductVariant\IProductVariantRepository;
use Modules\Product\Repositories\Category\ICategoryRepository;
use Modules\Product\Cache\Product\ProductCacheManager;
use Modules\Product\Exceptions\ProductNotFoundException;
use Modules\Product\Exceptions\ProductAccessDeniedException;
use Modules\Product\Exceptions\ProductValidationException;
use Modules\Product\Exceptions\ProductLimitExceededException;
use Modules\Product\Exceptions\VariantNotFoundException;
use Modules\Product\Exceptions\CategoryNotFoundException;
use Modules\Product\Exceptions\ProductTransactionException;
use Modules\Product\Enums\ProductTypeEnum;
use Modules\Product\Enums\ProductStatusEnum;
use App\Shared\Merchant\Services\IMerchantService;

/**
 * Product Service Implementation
 *
 * Comprehensive service that handles Product, Variant, and Pricing operations
 * for merchant-scoped data with proper business logic and validation.
 */
class ProductService implements IProductService
{
    public function __construct(
        private IProductRepository $productRepository,
        private IProductVariantRepository $variantRepository,
        private ICategoryRepository $categoryRepository,
        private IMerchantService $merchantService
    ) {}

    // ==========================================
    // PRODUCT OPERATIONS
    // ==========================================

    public function createProduct(array $productData, string $merchantId): Product
    {
        try {
            // Validate product data
            $validation = $this->validateProductData($productData, $merchantId);
            if (!$validation['valid']) {
                throw new ProductValidationException($validation['errors']);
            }

            // Validate category if provided
            if (isset($productData['category_id'])) {
                $this->validateCategoryExists($productData['category_id']);
            }

            // Validate product type if provided
            if (isset($productData['type'])) {
                $this->validateProductType($productData['type']);
            }

            // Check merchant limits
            $limits = $this->checkProductLimits($merchantId);
            if (!$limits['can_create']) {
                throw new ProductLimitExceededException($limits, $merchantId);
            }

            $product = DB::transaction(function () use ($productData, $merchantId) {
                try {
                    // Create product
                    $product = $this->productRepository->createForMerchant($productData, $merchantId);

                    // Create variants if provided
                    if (isset($productData['variants']) && is_array($productData['variants'])) {
                        foreach ($productData['variants'] as $variantData) {
                            $this->addVariant($product->id, $variantData, $merchantId);
                        }
                    }

                    // Set initial price if provided
                    if (isset($productData['price'])) {
                        $this->updateProductPrice($product->id, $productData['price'], $merchantId, 'initial');
                    }

                    // Initialize inventory if merchant uses inventory
                    $this->initializeInventory($product->id, $merchantId);

                    return $product;
                } catch (\Exception $e) {
                    Log::error('Failed to create product in transaction', [
                        'merchant_id' => $merchantId,
                        'product_data' => $productData,
                        'error' => $e->getMessage()
                    ]);
                    throw new ProductTransactionException('transaction_failed', ['operation' => 'create', 'error' => $e->getMessage()], 0, $e);
                }
            });

            Log::info('Product created successfully', [
                'product_id' => $product->id,
                'merchant_id' => $merchantId
            ]);

            // Return product with relationships
            return $this->getProductWithVariants($product->id, $merchantId);
        } catch (\Exception $e) {
            Log::error('Product creation failed', [
                'merchant_id' => $merchantId,
                'product_data' => $productData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateProduct(string $productId, array $productData, string $merchantId): Product
    {
        try {
            // Validate ownership
            $product = $this->productRepository->findByIdAndMerchant($productId, $merchantId);
            if (!$product) {
                throw new ProductAccessDeniedException($productId, $merchantId);
            }

            // Validate update data
            $validation = $this->validateProductData($productData, $merchantId);
            if (!$validation['valid']) {
                throw new ProductValidationException($validation['errors']);
            }

            // Validate category if provided
            if (isset($productData['category_id'])) {
                $this->validateCategoryExists($productData['category_id']);
            }

            // Validate product type if provided
            if (isset($productData['type'])) {
                $this->validateProductType($productData['type']);
            }

            DB::transaction(function () use ($productId, $productData, $merchantId) {
                try {
                    // Update product
                    $this->productRepository->updateForMerchant($productId, $productData, $merchantId);

                    // Update inventory if merchant uses inventory and relevant fields changed
                    if (
                        $this->merchantUsesInventory($merchantId) &&
                        (isset($productData['price']) || isset($productData['name']))
                    ) {
                        $this->updateInventory($productId, $merchantId, $productData);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to update product in transaction', [
                        'product_id' => $productId,
                        'merchant_id' => $merchantId,
                        'product_data' => $productData,
                        'error' => $e->getMessage()
                    ]);
                    throw new ProductTransactionException('transaction_failed', ['operation' => 'update', 'error' => $e->getMessage()], 0, $e);
                }
            });

            // Clear cache
            ProductCacheManager::invalidateForOperation('update', [
                'id' => $productId,
                'merchant_id' => $merchantId
            ]);

            Log::info('Product updated successfully', [
                'product_id' => $productId,
                'merchant_id' => $merchantId
            ]);

            return $this->getProductWithVariants($productId, $merchantId);
        } catch (\Exception $e) {
            Log::error('Product update failed', [
                'product_id' => $productId,
                'merchant_id' => $merchantId,
                'product_data' => $productData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function deleteProduct(string $productId, string $merchantId): bool
    {
        try {
            // Validate ownership
            $product = $this->productRepository->findByIdAndMerchant($productId, $merchantId);
            if (!$product) {
                return false;
            }

            // Check for active orders (simplified check)
            // In real implementation, check order relationships

            DB::transaction(function () use ($productId, $merchantId) {
                try {
                    // Delete variants first (cascade should handle this, but explicit for safety)
                    $variants = $this->variantRepository->getByProductId($productId);
                    foreach ($variants as $variant) {
                        $this->variantRepository->delete($variant->id);
                    }

                    // Delete product
                    $this->productRepository->deleteForMerchant($productId, $merchantId);

                    // Handle inventory cleanup if merchant uses inventory
                    if ($this->merchantUsesInventory($merchantId)) {
                        // Dummy implementation - in real, call inventory service to remove stock
                        Log::info("Cleaning up inventory for deleted product {$productId}", [
                            'merchant_id' => $merchantId
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to delete product in transaction', [
                        'product_id' => $productId,
                        'merchant_id' => $merchantId,
                        'error' => $e->getMessage()
                    ]);
                    throw new ProductTransactionException('transaction_failed', ['operation' => 'delete', 'error' => $e->getMessage()], 0, $e);
                }
            });

            ProductCacheManager::invalidateForOperation('delete', [
                'id' => $productId,
                'merchant_id' => $merchantId
            ]);

            Log::info('Product deleted successfully', [
                'product_id' => $productId,
                'merchant_id' => $merchantId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Product deletion failed', [
                'product_id' => $productId,
                'merchant_id' => $merchantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getProduct(string $productId, string $merchantId): ?Product
    {
        return $this->productRepository->findByIdAndMerchant($productId, $merchantId);
    }

    public function getProductWithVariants(string $productId, string $merchantId): ?Product
    {
        $product = $this->productRepository->findByIdAndMerchant($productId, $merchantId);

        if ($product) {
            $product->load(['variants', 'category']);

            // Add inventory data if merchant uses inventory
            if ($this->merchantUsesInventory($merchantId)) {
                $product->inventory = $this->getDummyInventoryData($productId);
            }

            // Add promo data
            $product->promos = $this->getDummyPromoData($productId);
        }

        return $product;
    }

    public function getMerchantProducts(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->productRepository->getByMerchantId($merchantId, $filters, $perPage);
    }

    public function duplicateProduct(string $productId, string $merchantId, array $overrides = []): Product
    {
        // Get source product
        $sourceProduct = $this->getProductWithVariants($productId, $merchantId);
        if (!$sourceProduct) {
            throw new \RuntimeException('Source product not found');
        }

        // Prepare duplicate data
        $duplicateData = array_merge($sourceProduct->toArray(), $overrides);
        unset($duplicateData['id'], $duplicateData['created_at'], $duplicateData['updated_at']);

        // Generate new unique data
        $duplicateData['name'] = $overrides['name'] ?? $sourceProduct->name . ' (Copy)';
        $duplicateData['slug'] = $this->generateSlug($duplicateData['name']);

        // Create duplicate
        return $this->createProduct($duplicateData, $merchantId);
    }

    public function toggleProductStatus(string $productId, bool $active, string $merchantId): bool
    {
        return $this->productRepository->updateForMerchant($productId, [
            'is_active' => $active
        ], $merchantId);
    }

    // ==========================================
    // VARIANT OPERATIONS
    // ==========================================

    public function addVariant(string $productId, array $variantData, string $merchantId): ProductVariant
    {
        // Validate product ownership
        $product = $this->productRepository->findByIdAndMerchant($productId, $merchantId);
        if (!$product) {
            throw new \RuntimeException('Product not found or access denied');
        }

        // Validate variant data
        $validation = $this->validateVariantData($variantData, $productId, $merchantId);
        if (!$validation['valid']) {
            throw new \InvalidArgumentException('Invalid variant data: ' . implode(', ', $validation['errors']));
        }

        // Generate SKU if not provided
        if (!isset($variantData['sku']) || empty($variantData['sku'])) {
            $variantData['sku'] = $this->generateVariantSku($product->sku ?? 'PRD', $variantData['attributes'] ?? []);
        }

        $variant = $this->variantRepository->createForProduct($variantData, $productId, $merchantId);

        // Clear product cache
        ProductCacheManager::invalidateForOperation('variant_added', [
            'product_id' => $productId,
            'merchant_id' => $merchantId
        ]);

        return $variant;
    }

    public function updateVariant(string $variantId, array $variantData, string $merchantId): ProductVariant
    {
        // Validate ownership through product
        $variant = $this->variantRepository->findById($variantId);
        if (!$variant || !$this->productRepository->findByIdAndMerchant($variant->product_id, $merchantId)) {
            throw new \RuntimeException('Variant not found or access denied');
        }

        $this->variantRepository->update($variantId, $variantData);

        return $variant->fresh();
    }

    public function removeVariant(string $variantId, string $merchantId): bool
    {
        // Validate ownership through product
        $variant = $this->variantRepository->findById($variantId);
        if (!$variant || !$this->productRepository->findByIdAndMerchant($variant->product_id, $merchantId)) {
            return false;
        }

        $result = $this->variantRepository->delete($variantId);

        if ($result) {
            ProductCacheManager::invalidateForOperation('variant_removed', [
                'product_id' => $variant->product_id,
                'merchant_id' => $merchantId
            ]);
        }

        return $result;
    }

    public function getProductVariants(string $productId, string $merchantId): Collection
    {
        // Validate product ownership
        if (!$this->productRepository->findByIdAndMerchant($productId, $merchantId)) {
            return collect();
        }

        return $this->variantRepository->getByProductId($productId);
    }

    public function updateVariantPrice(string $variantId, float $price, string $merchantId): bool
    {
        // Validate ownership through product
        $variant = $this->variantRepository->findById($variantId);
        if (!$variant || !$this->productRepository->findByIdAndMerchant($variant->product_id, $merchantId)) {
            return false;
        }

        return $this->variantRepository->update($variantId, ['price' => $price]);
    }

    public function generateVariantCombinations(string $productId, string $merchantId): Collection
    {
        // This would generate all possible combinations based on product attributes
        // Implementation depends on attribute system
        return collect();
    }

    // ==========================================
    // PRICING OPERATIONS
    // ==========================================

    public function updateProductPrice(string $productId, float $price, string $merchantId, string $reason = 'manual'): bool
    {
        // Validate ownership
        if (!$this->productRepository->findByIdAndMerchant($productId, $merchantId)) {
            return false;
        }

        return $this->productRepository->updateForMerchant($productId, [
            'price' => $price,
            'price_updated_at' => now(),
            'price_update_reason' => $reason
        ], $merchantId);
    }

    public function publishProduct(string $productId, string $merchantId): bool
    {
        // Validate ownership
        $product = $this->productRepository->findByIdAndMerchant($productId, $merchantId);
        if (!$product) {
            throw new ProductAccessDeniedException($productId, $merchantId);
        }

        // Business rule: Can only publish if product has valid data
        if (empty($product->name) || $product->price <= 0) {
            throw new ProductValidationException(['Product must have name and valid price to be published']);
        }

        return $this->productRepository->updateForMerchant($productId, [
            'status' => ProductStatusEnum::AVAILABLE->value,
            'published_at' => now()
        ], $merchantId);
    }

    public function unpublishProduct(string $productId, string $merchantId): bool
    {
        // Validate ownership
        if (!$this->productRepository->findByIdAndMerchant($productId, $merchantId)) {
            throw new ProductAccessDeniedException($productId, $merchantId);
        }

        return $this->productRepository->updateForMerchant($productId, [
            'status' => ProductStatusEnum::NOT_AVAILABLE->value,
            'unpublished_at' => now()
        ], $merchantId);
    }

    public function getProductPricing(string $productId): array
    {
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            return [];
        }

        $basePrice = $product->price ?? 0;
        $discountPercent = $product->discount_percent ?? 0;
        $discountedPrice = $basePrice * (1 - $discountPercent / 100);

        return [
            'product_id' => $productId,
            'base_price' => $basePrice,
            'discount_percent' => $discountPercent,
            'discounted_price' => $discountedPrice,
            'currency' => 'IDR', // Default currency
            'discount_active' => $product->discount_active ?? false,
        ];
    }

    public function calculateProductPrice(string $productId, array $modifiers = []): array
    {
        $pricing = $this->getProductPricing($productId);
        if (empty($pricing)) {
            return [];
        }

        $finalPrice = $pricing['discounted_price'];

        // Apply additional modifiers
        if (isset($modifiers['tax_rate'])) {
            $taxAmount = $finalPrice * ($modifiers['tax_rate'] / 100);
            $finalPrice += $taxAmount;
        }

        return array_merge($pricing, [
            'final_price' => $finalPrice,
            'modifiers_applied' => $modifiers,
        ]);
    }

    public function getPriceHistory(string $productId, array $dateRange = []): Collection
    {
        // This would require a price history table
        // For now, return empty collection
        return collect();
    }

    // ==========================================
    // BULK OPERATIONS
    // ==========================================

    public function bulkUpdateProducts(array $productIds, array $updateData, string $merchantId): array
    {
        $results = ['successful' => 0, 'failed' => 0, 'errors' => []];

        foreach ($productIds as $productId) {
            try {
                $this->productRepository->updateForMerchant($productId, $updateData, $merchantId);
                $results['successful']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Product {$productId}: " . $e->getMessage();
            }
        }

        return $results;
    }

    public function bulkUpdateVariantPrices(array $variantIds, float $priceAdjustment, string $merchantId): array
    {
        $results = ['successful' => 0, 'failed' => 0, 'errors' => []];

        foreach ($variantIds as $variantId) {
            try {
                // Validate ownership
                $variant = $this->variantRepository->findById($variantId);
                if ($variant && $this->productRepository->findByIdAndMerchant($variant->product_id, $merchantId)) {
                    $newPrice = $variant->price + $priceAdjustment;
                    $this->variantRepository->update($variantId, ['price' => $newPrice]);
                    $results['successful']++;
                } else {
                    throw new \RuntimeException('Variant not found or access denied');
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Variant {$variantId}: " . $e->getMessage();
            }
        }

        return $results;
    }

    // ==========================================
    // VALIDATION & BUSINESS RULES
    // ==========================================

    public function validateProductData(array $productData, string $merchantId): array
    {
        $errors = [];
        $valid = true;

        // Required fields
        if (empty($productData['name'])) {
            $errors[] = __('product::validation.name_required');
            $valid = false;
        }

        if (isset($productData['name']) && strlen($productData['name']) > 255) {
            $errors[] = __('product::validation.name_max', ['max' => 255]);
            $valid = false;
        }

        // Price validation
        if (isset($productData['price']) && $productData['price'] < 0) {
            $errors[] = __('product::validation.price_min', ['min' => 0]);
            $valid = false;
        }

        // SKU validation
        if (isset($productData['sku']) && strlen($productData['sku']) > 100) {
            $errors[] = __('product::validation.sku_max', ['max' => 100]);
            $valid = false;
        }

        // Barcode validation
        if (isset($productData['barcode']) && strlen($productData['barcode']) > 100) {
            $errors[] = __('product::validation.barcode_max', ['max' => 100]);
            $valid = false;
        }

        return [
            'valid' => $valid,
            'errors' => $errors,
            'warnings' => []
        ];
    }

    public function validateVariantData(array $variantData, string $productId, string $merchantId): array
    {
        $errors = [];
        $valid = true;

        // Required fields
        if (!isset($variantData['price']) || $variantData['price'] < 0) {
            $errors[] = __('product::validation.variant_price_min', ['min' => 0]);
            $valid = false;
        }

        // Name validation
        if (empty($variantData['name'])) {
            $errors[] = __('product::validation.variant_name_required');
            $valid = false;
        }

        // SKU uniqueness
        if (isset($variantData['sku']) && strlen($variantData['sku']) > 100) {
            $errors[] = __('product::validation.variant_sku_max', ['max' => 100]);
            $valid = false;
        }

        // Barcode uniqueness
        if (isset($variantData['barcode']) && strlen($variantData['barcode']) > 100) {
            $errors[] = __('product::validation.variant_barcode_max', ['max' => 100]);
            $valid = false;
        }

        return [
            'valid' => $valid,
            'errors' => $errors,
            'warnings' => []
        ];
    }

    public function checkProductLimits(string $merchantId): array
    {
        // Simplified limit checking
        // In real implementation, check subscription limits
        return [
            'can_create' => true,
            'current_count' => 0,
            'max_allowed' => 1000,
            'remaining' => 1000
        ];
    }

    public function generateSlug(string $name, ?string $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        // Check uniqueness logic here
        // For now, just return the slug
        return $slug;
    }

    public function generateVariantSku(string $productSku, array $attributes): string
    {
        $attributeString = '';
        if (!empty($attributes)) {
            $attributeString = '-' . implode('-', array_map('strtolower', $attributes));
        }

        return $productSku . $attributeString;
    }

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    /**
     * Validate category exists
     *
     * @param string $categoryId
     * @return void
     * @throws CategoryNotFoundException
     */
    private function validateCategoryExists(string $categoryId): void
    {
        $category = $this->categoryRepository->find($categoryId);
        if (!$category) {
            throw new CategoryNotFoundException("Category with ID {$categoryId} not found");
        }
    }

    /**
     * Validate product type
     *
     * @param string $type
     * @return void
     * @throws ProductValidationException
     */
    private function validateProductType(string $type): void
    {
        $validTypes = array_column(ProductTypeEnum::cases(), 'value');
        if (!in_array($type, $validTypes)) {
            throw new ProductValidationException(['type' => "Invalid product type. Allowed: " . implode(', ', $validTypes)]);
        }
    }

    /**
     * Check if merchant uses inventory
     *
     * @param string $merchantId
     * @return bool
     */
    private function merchantUsesInventory(string $merchantId): bool
    {
        $settings = $this->merchantService->getMerchantSetting($merchantId);
        return $settings['use_inventory'] ?? false;
    }

    /**
     * Get dummy inventory data for product
     *
     * @param string $productId
     * @return array
     */
    private function getDummyInventoryData(string $productId): array
    {
        // Dummy data - in real implementation, fetch from inventory module
        return [
            [
                'product_id' => $productId,
                'stock_quantity' => 100,
                'location' => 'Main Warehouse',
                'min_stock_level' => 10,
                'last_updated' => now()->toISOString()
            ],
            [
                'product_id' => $productId,
                'stock_quantity' => 50,
                'location' => 'Branch Store',
                'min_stock_level' => 5,
                'last_updated' => now()->toISOString()
            ]
        ];
    }

    /**
     * Get dummy promo data for product
     *
     * @param string $productId
     * @return array
     */
    private function getDummyPromoData(string $productId): array
    {
        // Dummy data - in real implementation, fetch from promo module
        return [
            [
                'product_id' => $productId,
                'discount_percentage' => 10,
                'discount_type' => 'percentage',
                'valid_from' => now()->toDateString(),
                'valid_until' => now()->addDays(30)->toDateString(),
                'is_active' => true
            ]
        ];
    }

    /**
     * Initialize inventory for new product
     *
     * @param string $productId
     * @param string $merchantId
     * @return void
     */
    private function initializeInventory(string $productId, string $merchantId): void
    {
        if (!$this->merchantUsesInventory($merchantId)) {
            return;
        }

        // Dummy implementation - in real, call inventory service
        Log::info("Initializing inventory for product {$productId}", [
            'merchant_id' => $merchantId,
            'inventory_data' => $this->getDummyInventoryData($productId)
        ]);
    }

    /**
     * Update inventory for product changes
     *
     * @param string $productId
     * @param string $merchantId
     * @param array $changes
     * @return void
     */
    private function updateInventory(string $productId, string $merchantId, array $changes): void
    {
        if (!$this->merchantUsesInventory($merchantId)) {
            return;
        }

        // Dummy implementation - in real, call inventory service
        Log::info("Updating inventory for product {$productId}", [
            'merchant_id' => $merchantId,
            'changes' => $changes,
            'inventory_data' => $this->getDummyInventoryData($productId)
        ]);
    }
}
