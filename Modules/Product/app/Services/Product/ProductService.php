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
use Modules\Product\Repositories\ProductImage\IProductImageRepository;
use Modules\Product\Services\FileUpload\IFileUploadService;
use Modules\Product\Exceptions\ProductNotFoundException;
use Modules\Product\Exceptions\ProductAccessDeniedException;
use Modules\Product\Exceptions\ProductValidationException;
use Modules\Product\Exceptions\ProductLimitExceededException;
use Modules\Product\Exceptions\VariantNotFoundException;
use Modules\Product\Exceptions\CategoryNotFoundException;
use Modules\Product\Exceptions\ProductTransactionException;
use Modules\Product\Enums\ProductTypeEnum;
use Modules\Product\Enums\ProductStatusEnum;
use Modules\Product\Events\ProductCreated;
use Modules\Product\Events\ProductUpdated;
use App\Shared\Merchant\Services\IMerchantService;
use App\Shared\Setting\Services\ISettingService;

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
        private IMerchantService $merchantService,
        private ISettingService $settingService,
        private IFileUploadService $fileUploadService,
        private IProductImageRepository $productImageRepository
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

                    // Handle product images
                    if (isset($productData['images']) && is_array($productData['images'])) {
                        foreach ($productData['images'] as $index => $imageFile) {
                            if ($imageFile instanceof \Illuminate\Http\UploadedFile) {
                                $uploaded = $this->fileUploadService->uploadProductImage($imageFile, $merchantId, $product->id);
                                $this->productImageRepository->create([
                                    'product_id' => $product->id,
                                    'path' => $uploaded['path'],
                                    'url' => $uploaded['url'],
                                    'is_primary' => $index === 0, // First image is primary
                                    'sort_order' => $index,
                                    'metadata' => [
                                        'filename' => $uploaded['filename'],
                                        'size' => $uploaded['size'],
                                        'mime_type' => $uploaded['mime_type']
                                    ]
                                ]);
                            }
                        }
                    }

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

            event(new ProductCreated($product));

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

                    // Handle new images
                    if (isset($productData['new_images']) && is_array($productData['new_images'])) {
                        // Get current max sort order
                        $currentImages = $this->productImageRepository->getByProductId($productId);
                        $maxSortOrder = $currentImages->max('sort_order') ?? -1;

                        foreach ($productData['new_images'] as $index => $imageFile) {
                            if ($imageFile instanceof \Illuminate\Http\UploadedFile) {
                                $uploaded = $this->fileUploadService->uploadProductImage($imageFile, $merchantId, $productId);
                                $this->productImageRepository->create([
                                    'product_id' => $productId,
                                    'path' => $uploaded['path'],
                                    'url' => $uploaded['url'],
                                    'is_primary' => $currentImages->isEmpty() && $index === 0,
                                    'sort_order' => $maxSortOrder + 1 + $index,
                                    'metadata' => [
                                        'filename' => $uploaded['filename'],
                                        'size' => $uploaded['size'],
                                        'mime_type' => $uploaded['mime_type']
                                    ]
                                ]);
                            }
                        }
                    }

                    // Handle deleted images
                    if (isset($productData['deleted_images']) && is_array($productData['deleted_images'])) {
                        foreach ($productData['deleted_images'] as $imageId) {
                            $image = $this->productImageRepository->findById($imageId);
                            if ($image && $image->product_id === $productId) {
                                $this->fileUploadService->deleteProductImage($image->path);
                                $this->productImageRepository->delete($imageId);
                            }
                        }
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

            // Cache operations disabled

            Log::info('Product updated successfully', [
                'product_id' => $productId,
                'merchant_id' => $merchantId
            ]);

            $product = $this->getProductWithVariants($productId, $merchantId);
            event(new ProductUpdated($product));

            return $product;
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
                } catch (\Exception $e) {
                    Log::error('Failed to delete product in transaction', [
                        'product_id' => $productId,
                        'merchant_id' => $merchantId,
                        'error' => $e->getMessage()
                    ]);
                    throw new ProductTransactionException('transaction_failed', ['operation' => 'delete', 'error' => $e->getMessage()], 0, $e);
                }
            });

            // Cache operations disabled

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
            $product->setRelation('images', $this->productImageRepository->getByProductId($productId));
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

        // Cache operations disabled

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

        // Cache operations disabled

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
        // Get limits from setting service
        $maxProducts = (int) $this->settingService->getValue('product.limit_per_merchant', 1000);

        // Count current products for merchant
        // We need a count method in repository, but for now we can use getByMerchantId with pagination 1 which returns count in paginator
        // Or better, add countByMerchantId to repository.
        // For now, using a less efficient way via existing method if count not available
        // Ideally: $currentCount = $this->productRepository->countByMerchantId($merchantId);

        // Using existing method with minimal data
        $paginator = $this->productRepository->getByMerchantId($merchantId, [], 1);
        $currentCount = $paginator->total();

        return [
            'can_create' => $currentCount < $maxProducts,
            'current_count' => $currentCount,
            'max_allowed' => $maxProducts,
            'remaining' => max(0, $maxProducts - $currentCount)
        ];
    }

    public function generateSlug(string $name, ?string $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        // Check uniqueness via repository
        while ($this->productRepository->existsBySlug($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

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
}
