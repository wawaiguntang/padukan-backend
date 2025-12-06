# Product Module Service Specifications

## Overview

This document contains detailed specifications for all services in the Product module. Each service includes interface definitions, method signatures, and detailed descriptions of functionality.

## Table of Contents

1. [CategoryManagementService](#1-categorymanagementservice)
2. [ProductManagementService](#2-productmanagementservice)
3. [ProductVariantManagementService](#3-productvariantmanagementservice)
4. [ProductSearchService](#4-productsearchservice)
5. [ProductInventoryService](#5-productinventoryservice)
6. [ProductPricingService](#6-productpricingservice)
7. [MerchantProductService](#7-merchantproductservice)
8. [ProductAuthorizationService](#8-productauthorizationservice)
9. [Business Logic Services](business_logic_services.md) - AI-powered intelligence and automation services
10. [Elasticsearch Integration](elasticsearch_integration.md) - Search index management and data synchronization

---

## 1. CategoryManagementService

### Interface Definition

```php
interface ICategoryManagementService {
    public function getCategories(?string $parentId = null): Collection;
    public function getCategoryById(string $categoryId): ?Category;
    public function getCategoryHierarchy(string $categoryId): ?Category;
    public function createCategory(array $categoryData, string $merchantId): Category;
    public function updateCategory(string $categoryId, array $categoryData, string $merchantId): Category;
    public function deleteCategory(string $categoryId, string $merchantId): bool;
    public function getCategoryTree(): Collection;
    public function getCategoryPath(string $categoryId): Collection;
    public function moveCategory(string $categoryId, ?string $newParentId, string $merchantId): bool;
    public function validateCategoryHierarchy(string $categoryId, ?string $parentId): bool;
}
```

### Method Specifications

#### `getCategories(?string $parentId = null): Collection`

**Purpose**: Retrieve categories based on parent ID for hierarchical navigation

**Parameters**:

-   `$parentId` (string|null): Parent category UUID, null for root categories

**Returns**: `Collection<Category>` - Collection of category models

**Business Rules**:

-   Filters by active categories only
-   Orders by creation date descending
-   Includes basic category information

#### `getCategoryById(string $categoryId): ?Category`

**Purpose**: Get detailed category information by ID

**Parameters**:

-   `$categoryId` (string): Category UUID

**Returns**: `Category|null` - Category model or null if not found

**Business Rules**:

-   Includes soft-deleted categories for admin access
-   Loads basic relationships (parent, children count)

#### `getCategoryHierarchy(string $categoryId): ?Category`

**Purpose**: Get category with full parent-child relationships loaded

**Parameters**:

-   `$categoryId` (string): Category UUID

**Returns**: `Category|null` - Category with relationships or null

**Business Rules**:

-   Eager loads parent and children relationships
-   Used for detailed category management

#### `createCategory(array $categoryData, string $merchantId): Category`

**Purpose**: Create new category with validation

**Parameters**:

-   `$categoryData` (array): Category data
    -   `name` (string, required): Category name
    -   `slug` (string, optional): URL slug, auto-generated if not provided
    -   `description` (string, optional): Category description
    -   `parent_id` (string, optional): Parent category UUID
-   `$merchantId` (string): Merchant UUID for ownership

**Returns**: `Category` - Created category model

**Validations**:

-   Name: required, max 255 characters
-   Slug: unique, auto-generated from name
-   Parent ID: must exist if provided
-   Merchant ownership validation

**Business Rules**:

-   Auto-generates unique slug
-   Sets initial level based on parent
-   Updates category path

#### `updateCategory(string $categoryId, array $categoryData, string $merchantId): Category`

**Purpose**: Update existing category

**Parameters**:

-   `$categoryId` (string): Category UUID
-   `$categoryData` (array): Updated category data (same structure as create)
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `Category` - Updated category model

**Business Rules**:

-   Validates ownership
-   Regenerates slug if name changed
-   Updates path if parent changed
-   Updates descendant paths if hierarchy changed

#### `deleteCategory(string $categoryId, string $merchantId): bool`

**Purpose**: Soft delete category

**Parameters**:

-   `$categoryId` (string): Category UUID
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `bool` - Success status

**Business Rules**:

-   Cannot delete if has child categories
-   Cannot delete if has associated products
-   Soft delete (sets deleted_at)

#### `getCategoryTree(): Collection`

**Purpose**: Get complete category tree for navigation

**Returns**: `Collection` - Nested collection of all categories

**Business Rules**:

-   Builds hierarchical tree structure
-   Cached for performance
-   Used for category navigation menus

#### `getCategoryPath(string $categoryId): Collection`

**Purpose**: Get breadcrumb path from root to category

**Parameters**:

-   `$categoryId` (string): Category UUID

**Returns**: `Collection<Category>` - Ordered collection from root to target

**Business Rules**:

-   Traverses parent relationships
-   Used for breadcrumb navigation

#### `moveCategory(string $categoryId, ?string $newParentId, string $merchantId): bool`

**Purpose**: Move category to new parent in hierarchy

**Parameters**:

-   `$categoryId` (string): Category UUID to move
-   `$newParentId` (string|null): New parent UUID or null for root
-   `$merchantId` (string): Merchant UUID for validation

**Returns**: `bool` - Success status

**Business Rules**:

-   Validates no circular references
-   Updates category level
-   Updates paths for category and all descendants
-   Validates merchant ownership

#### `validateCategoryHierarchy(string $categoryId, ?string $parentId): bool`

**Purpose**: Validate category hierarchy integrity

**Parameters**:

-   `$categoryId` (string): Category UUID
-   `$parentId` (string|null): Proposed parent UUID

**Returns**: `bool` - Validation result

**Business Rules**:

-   Prevents circular references
-   Validates parent exists and is active
-   Checks hierarchy depth limits

---

## 2. ProductManagementService

### Interface Definition

```php
interface IProductManagementService {
    public function getProducts(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getProductById(string $productId): ?Product;
    public function getProductWithRelations(string $productId): ?Product;
    public function createProduct(array $productData, string $merchantId): Product;
    public function updateProduct(string $productId, array $productData, string $merchantId): Product;
    public function deleteProduct(string $productId, string $merchantId): bool;
    public function validateProductData(array $productData, string $merchantId): array;
    public function generateProductSlug(string $name, ?string $excludeId = null): string;
    public function updateProductVersion(string $productId): bool;
    public function toggleProductStatus(string $productId, bool $active, string $merchantId): bool;
    public function duplicateProduct(string $productId, string $merchantId): Product;
    public function bulkUpdateProducts(array $productIds, array $updateData, string $merchantId): int;
    public function getProductStatistics(string $merchantId): array;
}
```

### Method Specifications

#### `getProducts(array $filters = [], int $perPage = 20): LengthAwarePaginator`

**Purpose**: Get paginated list of products with advanced filtering

**Parameters**:

-   `$filters` (array): Filter criteria
    -   `merchant_id` (string, optional): Filter by merchant
    -   `category_id` (string, optional): Filter by category
    -   `type` (string, optional): Product type ('food', 'mart', 'service')
    -   `status` (string, optional): 'active', 'inactive'
    -   `search` (string, optional): Full-text search query
    -   `price_min` (float, optional): Minimum price
    -   `price_max` (float, optional): Maximum price
    -   `has_variant` (bool, optional): Filter by variant availability
-   `$perPage` (int): Items per page (default: 20)

**Returns**: `LengthAwarePaginator<Product>` - Paginated product collection

**Business Rules**:

-   Applies ownership filtering for non-admin users
-   Supports complex multi-field search
-   Orders by creation date descending

#### `getProductById(string $productId): ?Product`

**Purpose**: Get product details by ID

**Parameters**:

-   `$productId` (string): Product UUID

**Returns**: `Product|null` - Product model or null

**Business Rules**:

-   Includes soft-deleted products for admin access
-   Basic product information without relationships

#### `getProductWithRelations(string $productId): ?Product`

**Purpose**: Get product with all relationships loaded

**Parameters**:

-   `$productId` (string): Product UUID

**Returns**: `Product|null` - Product with relationships

**Relationships Loaded**:

-   `category` - Product category
-   `variants` - Product variants
-   `extras` - Product extras
-   `serviceDetails` - Service-specific details

#### `createProduct(array $productData, string $merchantId): Product`

**Purpose**: Create new product with validation

**Parameters**:

-   `$productData` (array): Product data
    -   `name` (string, required): Product name
    -   `description` (string, optional): Product description
    -   `type` (string, required): 'food', 'mart', or 'service'
    -   `category_id` (string, optional): Category UUID
    -   `price` (float, optional): Base price
    -   `sku` (string, optional): Stock keeping unit
    -   `barcode` (string, optional): Product barcode
    -   `has_variant` (bool, optional): Has variants flag
    -   `metadata` (array, optional): Additional metadata
-   `$merchantId` (string): Merchant UUID for ownership

**Returns**: `Product` - Created product model

**Validations**:

-   Name: required, max 255 characters
-   Type: must be valid enum value
-   SKU: unique per merchant if provided
-   Barcode: unique per merchant if provided
-   Category: must exist if provided
-   Price: numeric, >= 0 if provided

**Business Rules**:

-   Auto-generates unique slug
-   Sets version to 1
-   Validates merchant ownership

#### `updateProduct(string $productId, array $productData, string $merchantId): Product`

**Purpose**: Update existing product

**Parameters**:

-   `$productId` (string): Product UUID
-   `$productData` (array): Updated data (same structure as create)
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `Product` - Updated product model

**Business Rules**:

-   Increments version number
-   Regenerates slug if name changed
-   Validates ownership and data integrity

#### `deleteProduct(string $productId, string $merchantId): bool`

**Purpose**: Soft delete product

**Parameters**:

-   `$productId` (string): Product UUID
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `bool` - Success status

**Business Rules**:

-   Checks for active orders before deletion
-   Soft delete preserves data integrity

#### `validateProductData(array $productData, string $merchantId): array`

**Purpose**: Validate product data against business rules

**Parameters**:

-   `$productData` (array): Product data to validate
-   `$merchantId` (string): Merchant UUID for uniqueness checks

**Returns**: `array` - Array of validation errors (empty if valid)

**Validation Rules**:

-   Required field validation
-   Data type validation
-   Uniqueness constraints (SKU, barcode per merchant)
-   Foreign key validation (category existence)

#### `generateProductSlug(string $name, ?string $excludeId = null): string`

**Purpose**: Generate unique URL slug from product name

**Parameters**:

-   `$name` (string): Product name
-   `$excludeId` (string|null): Product ID to exclude from uniqueness check

**Returns**: `string` - Unique slug

**Algorithm**:

-   Convert to lowercase
-   Replace spaces with hyphens
-   Remove special characters
-   Append counter if duplicate exists

#### `updateProductVersion(string $productId): bool`

**Purpose**: Increment product version for change tracking

**Parameters**:

-   `$productId` (string): Product UUID

**Returns**: `bool` - Success status

**Business Rules**:

-   Used for optimistic locking
-   Tracks change history

#### `toggleProductStatus(string $productId, bool $active, string $merchantId): bool`

**Purpose**: Enable/disable product availability

**Parameters**:

-   `$productId` (string): Product UUID
-   `$active` (bool): New active status
-   `$merchantId` (string): Merchant UUID for validation

**Returns**: `bool` - Success status

**Business Rules**:

-   Updates has_expired field
-   Triggers cache invalidation

#### `duplicateProduct(string $productId, string $merchantId): Product`

**Purpose**: Create copy of existing product

**Parameters**:

-   `$productId` (string): Source product UUID
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `Product` - New duplicated product

**Business Rules**:

-   Copies all product data except ID
-   Generates new unique slug
-   Resets version to 1
-   Optional: copy variants and extras

#### `bulkUpdateProducts(array $productIds, array $updateData, string $merchantId): int`

**Purpose**: Update multiple products simultaneously

**Parameters**:

-   `$productIds` (array): Array of product UUIDs
-   `$updateData` (array): Limited update data
    -   `status` (bool, optional): Active status
    -   `category_id` (string, optional): New category
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `int` - Number of successfully updated products

**Business Rules**:

-   Validates ownership for all products
-   Transaction safety with rollback on failure

#### `getProductStatistics(string $merchantId): array`

**Purpose**: Get dashboard statistics for merchant

**Parameters**:

-   `$merchantId` (string): Merchant UUID

**Returns**: `array` - Statistics data

```php
[
    'total_products' => int,
    'active_products' => int,
    'inactive_products' => int,
    'products_with_variants' => int,
    'total_variants' => int,
    'low_stock_products' => int
]
```

**Business Rules**:

-   Real-time calculation
-   Cached for performance

---

## 3. ProductVariantManagementService

### Interface Definition

```php
interface IProductVariantManagementService {
    public function getVariantsByProduct(string $productId): Collection;
    public function getVariantById(string $variantId): ?ProductVariant;
    public function createVariant(array $variantData, string $productId, string $merchantId): ProductVariant;
    public function updateVariant(string $variantId, array $variantData, string $merchantId): ProductVariant;
    public function deleteVariant(string $variantId, string $merchantId): bool;
    public function validateVariantData(array $variantData, string $productId): array;
    public function generateVariantSku(string $productSku, array $attributes): string;
    public function checkAttributeCombinationExists(array $attributes, string $productId, ?string $excludeId = null): bool;
    public function updateVariantPrice(string $variantId, float $price, string $merchantId): bool;
    public function bulkUpdateVariantPrices(array $variantIds, float $priceAdjustment, string $merchantId): int;
    public function getVariantCombinations(string $productId): Collection;
    public function validateVariantStock(string $variantId, int $requestedQty): bool;
}
```

### Method Specifications

#### `getVariantsByProduct(string $productId): Collection`

**Purpose**: Get all variants for a specific product

**Parameters**:

-   `$productId` (string): Product UUID

**Returns**: `Collection<ProductVariant>` - Collection of variant models

**Business Rules**:

-   Excludes expired variants
-   Orders by creation date
-   Includes basic variant information

#### `getVariantById(string $variantId): ?ProductVariant`

**Purpose**: Get variant details by ID

**Parameters**:

-   `$variantId` (string): Variant UUID

**Returns**: `ProductVariant|null` - Variant model or null

**Business Rules**:

-   Includes soft-deleted variants for admin access
-   Loads product relationship

#### `createVariant(array $variantData, string $productId, string $merchantId): ProductVariant`

**Purpose**: Create new product variant

**Parameters**:

-   `$variantData` (array): Variant data
    -   `name` (string, required): Variant name
    -   `price` (float, required): Variant price
    -   `sku` (string, optional): Variant SKU
    -   `barcode` (string, optional): Variant barcode
    -   `attribute_master_ids` (array, optional): Master attribute IDs
    -   `attribute_custom_ids` (array, optional): Custom attribute IDs
    -   `unit` (string, optional): Unit of measurement
    -   `conversion_id` (string, optional): Unit conversion ID
    -   `metadata` (array, optional): Additional metadata
-   `$productId` (string): Parent product UUID
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `ProductVariant` - Created variant model

**Validations**:

-   Name: required, max 255 characters
-   Price: required, numeric, > 0
-   SKU: unique across all variants if provided
-   Barcode: unique across all variants if provided
-   Attribute combinations: unique per product
-   Product ownership validation

**Business Rules**:

-   Auto-generates SKU if not provided
-   Validates attribute combination uniqueness
-   Sets default version to 1

#### `updateVariant(string $variantId, array $variantData, string $merchantId): ProductVariant`

**Purpose**: Update existing variant

**Parameters**:

-   `$variantId` (string): Variant UUID
-   `$variantData` (array): Updated data (same structure as create)
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `ProductVariant` - Updated variant model

**Business Rules**:

-   Increments version number
-   Re-validates attribute combinations
-   Updates product cache

#### `deleteVariant(string $variantId, string $merchantId): bool`

**Purpose**: Delete product variant

**Parameters**:

-   `$variantId` (string): Variant UUID
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `bool` - Success status

**Business Rules**:

-   Checks for active orders before deletion
-   Soft delete preserves data integrity

#### `validateVariantData(array $variantData, string $productId): array`

**Purpose**: Validate variant data against business rules

**Parameters**:

-   `$variantData` (array): Variant data to validate
-   `$productId` (string): Parent product UUID

**Returns**: `array` - Array of validation errors (empty if valid)

**Validation Rules**:

-   Required field validation
-   SKU uniqueness across all variants
-   Barcode uniqueness across all variants
-   Attribute combination uniqueness per product
-   Price validation (> 0)

#### `generateVariantSku(string $productSku, array $attributes): string`

**Purpose**: Generate SKU for variant based on attributes

**Parameters**:

-   `$productSku` (string): Base product SKU
-   `$attributes` (array): Attribute values array

**Returns**: `string` - Generated SKU

**Format**: `{productSku}-{attribute1}-{attribute2}`

#### `checkAttributeCombinationExists(array $attributes, string $productId, ?string $excludeId = null): bool`

**Purpose**: Check if attribute combination already exists for product

**Parameters**:

-   `$attributes` (array): Attribute combination
-   `$productId` (string): Product UUID
-   `$excludeId` (string|null): Variant ID to exclude from check

**Returns**: `bool` - Existence status

**Business Rules**:

-   Compares attribute_master_ids and attribute_custom_ids
-   Used for preventing duplicate variants

#### `updateVariantPrice(string $variantId, float $price, string $merchantId): bool`

**Purpose**: Update variant price

**Parameters**:

-   `$variantId` (string): Variant UUID
-   `$price` (float): New price
-   `$merchantId` (string): Merchant UUID for validation

**Returns**: `bool` - Success status

**Business Rules**:

-   Price must be > 0
-   Triggers price history logging

#### `bulkUpdateVariantPrices(array $variantIds, float $priceAdjustment, string $merchantId): int`

**Purpose**: Bulk price adjustment for multiple variants

**Parameters**:

-   `$variantIds` (array): Array of variant UUIDs
-   `$priceAdjustment` (float): Price adjustment (absolute or percentage)
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `int` - Number of successfully updated variants

**Business Rules**:

-   Validates ownership for all variants
-   Supports both absolute and percentage adjustments

#### `getVariantCombinations(string $productId): Collection`

**Purpose**: Get all possible attribute combinations for product

**Parameters**:

-   `$productId` (string): Product UUID

**Returns**: `Collection` - Possible combinations array

**Business Rules**:

-   Based on master attributes configuration
-   Used for variant creation suggestions

#### `validateVariantStock(string $variantId, int $requestedQty): bool`

**Purpose**: Check if variant has sufficient stock

**Parameters**:

-   `$variantId` (string): Variant UUID
-   `$requestedQty` (int): Requested quantity

**Returns**: `bool` - Availability status

**Business Rules**:

-   Available stock = Current - Reserved
-   Used for order validation

---

## 4. ProductSearchService

### Interface Definition

```php
interface IProductSearchService {
    public function searchProducts(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function searchByCategory(string $categoryId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function searchByMerchant(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function advancedSearch(array $criteria, int $perPage = 20): LengthAwarePaginator;
    public function getSearchSuggestions(string $query, int $limit = 10): Collection;
    public function getPopularSearchTerms(int $limit = 20): Collection;
    public function getAvailableFilters(): array;
    public function getPriceRange(?string $categoryId = null): array;
    public function getCategoryFacets(): Collection;
    public function getAttributeFacets(string $attributeKey): Collection;
    public function logSearchQuery(string $query, array $filters = [], int $resultCount = 0): void;
    public function getSearchAnalytics(\DateTime $startDate, \DateTime $endDate): array;
}
```

### Method Specifications

#### `searchProducts(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator`

**Purpose**: Perform full-text search across products

**Parameters**:

-   `$query` (string): Search query string
-   `$filters` (array): Additional filters
    -   `category_ids` (array, optional): Category UUIDs
    -   `merchant_ids` (array, optional): Merchant UUIDs
    -   `price_min` (float, optional): Minimum price
    -   `price_max` (float, optional): Maximum price
    -   `product_types` (array, optional): Product types
-   `$perPage` (int): Results per page (default: 20)

**Returns**: `LengthAwarePaginator` - Paginated search results

**Search Fields**: name, description, category names, merchant name

**Business Rules**:

-   Relevance-based scoring
-   Fuzzy matching for typos
-   Result highlighting

#### `searchByCategory(string $categoryId, array $filters = [], int $perPage = 20): LengthAwarePaginator`

**Purpose**: Search products within specific category

**Parameters**:

-   `$categoryId` (string): Category UUID
-   `$filters` (array): Same as searchProducts
-   `$perPage` (int): Results per page

**Returns**: `LengthAwarePaginator` - Category-specific results

**Business Rules**:

-   Includes subcategory products
-   Category-specific ranking

#### `searchByMerchant(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator`

**Purpose**: Search products for specific merchant

**Parameters**:

-   `$merchantId` (string): Merchant UUID
-   `$filters` (array): Same as searchProducts
-   `$perPage` (int): Results per page

**Returns**: `LengthAwarePaginator` - Merchant-specific results

**Business Rules**:

-   Ownership validation
-   Merchant-specific optimizations

#### `advancedSearch(array $criteria, int $perPage = 20): LengthAwarePaginator`

**Purpose**: Multi-criteria advanced search

**Parameters**:

-   `$criteria` (array): Advanced search criteria
    -   `query` (string, optional): Full-text search
    -   `category_ids` (array, optional): Category filters
    -   `merchant_ids` (array, optional): Merchant filters
    -   `price_min` (float, optional): Price range start
    -   `price_max` (float, optional): Price range end
    -   `product_types` (array, optional): Product type filters
    -   `attributes` (array, optional): Attribute filters
    -   `in_stock` (bool, optional): Stock availability
    -   `has_variants` (bool, optional): Variant availability
    -   `rating_min` (float, optional): Minimum rating
-   `$perPage` (int): Results per page

**Returns**: `LengthAwarePaginator` - Advanced search results

**Business Rules**:

-   Complex query building
-   Multiple filter combinations
-   Performance optimization

#### `getSearchSuggestions(string $query, int $limit = 10): Collection`

**Purpose**: Get autocomplete suggestions

**Parameters**:

-   `$query` (string): Partial search query
-   `$limit` (int): Maximum suggestions (default: 10)

**Returns**: `Collection<string>` - Suggestion strings

**Sources**:

-   Product names
-   Category names
-   Popular search terms
-   Brand names

#### `getPopularSearchTerms(int $limit = 20): Collection`

**Purpose**: Get trending search terms

**Parameters**:

-   `$limit` (int): Maximum terms to return (default: 20)

**Returns**: `Collection` - Search terms with frequency

```php
[
    ['term' => 'nasi goreng', 'count' => 150],
    ['term' => 'ayam bakar', 'count' => 89]
]
```

**Business Rules**:

-   Last 30 days data
-   Minimum search frequency threshold

#### `getAvailableFilters(): array`

**Purpose**: Get all available filter options

**Returns**: `array` - Filter configuration

```php
[
    'categories' => [...],
    'price_ranges' => [...],
    'product_types' => [...],
    'attributes' => [...]
]
```

#### `getPriceRange(?string $categoryId = null): array`

**Purpose**: Get price range for filtering

**Parameters**:

-   `$categoryId` (string|null): Optional category filter

**Returns**: `array` - Price range data

```php
[
    'min' => 1000.00,
    'max' => 500000.00,
    'currency' => 'IDR'
]
```

#### `getCategoryFacets(): Collection`

**Purpose**: Get category facets with product counts

**Returns**: `Collection` - Categories with counts

```php
[
    ['id' => 'uuid', 'name' => 'Makanan', 'count' => 150],
    ['id' => 'uuid', 'name' => 'Minuman', 'count' => 89]
]
```

#### `getAttributeFacets(string $attributeKey): Collection`

**Purpose**: Get attribute values with counts

**Parameters**:

-   `$attributeKey` (string): Attribute key (e.g., 'size', 'color')

**Returns**: `Collection` - Attribute values with counts

```php
[
    ['value' => 'L', 'name' => 'Large', 'count' => 45],
    ['value' => 'M', 'name' => 'Medium', 'count' => 32]
]
```

#### `logSearchQuery(string $query, array $filters = [], int $resultCount = 0): void`

**Purpose**: Log search queries for analytics

**Parameters**:

-   `$query` (string): Search query
-   `$filters` (array): Applied filters
-   `$resultCount` (int): Number of results returned

**Returns**: `void`

**Business Rules**:

-   Async logging
-   Rate limiting to prevent spam
-   IP-based filtering

#### `getSearchAnalytics(\DateTime $startDate, \DateTime $endDate): array`

**Purpose**: Get search performance analytics

**Parameters**:

-   `$startDate` (\DateTime): Analysis start date
-   `$endDate` (\DateTime): Analysis end date

**Returns**: `array` - Analytics data

```php
[
    'total_searches' => 15420,
    'unique_queries' => 2340,
    'no_result_queries' => 450,
    'popular_terms' => [...],
    'conversion_rate' => 0.15,
    'average_response_time' => 0.234
]
```

**Business Rules**:

-   Comprehensive search metrics
-   Performance tracking
-   Conversion analysis

---

## 5. ProductInventoryService

### Interface Definition

```php
interface IProductInventoryService {
    public function getProductStock(string $productId): array;
    public function getVariantStock(string $variantId): array;
    public function updateProductStock(string $productId, int $quantity, string $merchantId, string $reason = 'manual'): bool;
    public function updateVariantStock(string $variantId, int $quantity, string $merchantId, string $reason = 'manual'): bool;
    public function checkStockAvailability(string $productId, int $quantity, ?string $variantId = null): bool;
    public function reserveStock(string $productId, int $quantity, string $orderId, ?string $variantId = null): bool;
    public function releaseReservedStock(string $orderId): bool;
    public function getLowStockProducts(string $merchantId, int $threshold = 10): Collection;
    public function getOutOfStockProducts(string $merchantId): Collection;
    public function sendLowStockAlert(string $productId): bool;
    public function getInventoryReport(string $merchantId, array $filters = []): array;
    public function getStockMovementHistory(string $productId, \DateTime $startDate, \DateTime $endDate): Collection;
    public function bulkUpdateStock(array $stockUpdates, string $merchantId): array;
    public function importStockFromFile(string $filePath, string $merchantId): array;
}
```

### Method Specifications

#### `getProductStock(string $productId): array`

**Purpose**: Get comprehensive stock information for product

**Parameters**:

-   `$productId` (string): Product UUID

**Returns**: `array` - Stock information

```php
[
    'product_id' => 'uuid',
    'current_stock' => 150,
    'reserved_stock' => 25,
    'available_stock' => 125,
    'low_stock_threshold' => 20,
    'is_low_stock' => false,
    'last_updated' => '2025-12-06T09:00:00Z',
    'variants' => [...] // Variant stock summary
]
```

#### `getVariantStock(string $variantId): array`

**Purpose**: Get stock information for specific variant

**Parameters**:

-   `$variantId` (string): Variant UUID

**Returns**: `array` - Variant stock information (same structure as product)

#### `updateProductStock(string $productId, int $quantity, string $merchantId, string $reason = 'manual'): bool`

**Purpose**: Update product stock level

**Parameters**:

-   `$productId` (string): Product UUID
-   `$quantity` (int): New stock quantity (can be negative for deductions)
-   `$merchantId` (string): Merchant UUID for ownership validation
-   `$reason` (string): Update reason ('manual', 'sale', 'return', 'adjustment')

**Returns**: `bool` - Success status

**Business Rules**:

-   Logs stock movement history
-   Triggers low stock alerts if threshold reached
-   Updates cache and search index

#### `updateVariantStock(string $variantId, int $quantity, string $merchantId, string $reason = 'manual'): bool`

**Purpose**: Update variant stock level

**Parameters**: Same as updateProductStock

**Returns**: `bool` - Success status

**Business Rules**: Same as product stock update

#### `checkStockAvailability(string $productId, int $quantity, ?string $variantId = null): bool`

**Purpose**: Check if sufficient stock is available

**Parameters**:

-   `$productId` (string): Product UUID
-   `$quantity` (int): Required quantity
-   `$variantId` (string|null): Specific variant UUID

**Returns**: `bool` - Availability status

**Calculation**: Available = Current - Reserved

#### `reserveStock(string $productId, int $quantity, string $orderId, ?string $variantId = null): bool`

**Purpose**: Reserve stock for pending order

**Parameters**:

-   `$productId` (string): Product UUID
-   `$quantity` (int): Quantity to reserve
-   `$orderId` (string): Order UUID for tracking
-   `$variantId` (string|null): Specific variant UUID

**Returns**: `bool` - Reservation success status

**Business Rules**:

-   Prevents overselling
-   Reservation expires automatically
-   Tracks reservation history

#### `releaseReservedStock(string $orderId): bool`

**Purpose**: Release stock reservation

**Parameters**:

-   `$orderId` (string): Order UUID

**Returns**: `bool` - Release success status

**Business Rules**:

-   Called when order is cancelled or fulfilled
-   Restores stock availability

#### `getLowStockProducts(string $merchantId, int $threshold = 10): Collection`

**Purpose**: Get products with low stock levels

**Parameters**:

-   `$merchantId` (string): Merchant UUID
-   `$threshold` (int): Low stock threshold (default: 10)

**Returns**: `Collection` - Low stock products

**Business Rules**:

-   Available stock <= threshold
-   Excludes out-of-stock items

#### `getOutOfStockProducts(string $merchantId): Collection`

**Purpose**: Get products that are completely out of stock

**Parameters**:

-   `$merchantId` (string): Merchant UUID

**Returns**: `Collection` - Out of stock products

**Business Rules**:

-   Available stock <= 0
-   Includes reserved stock information

#### `sendLowStockAlert(string $productId): bool`

**Purpose**: Send low stock notification to merchant

**Parameters**:

-   `$productId` (string): Product UUID

**Returns**: `bool` - Notification success status

**Business Rules**:

-   Rate limiting to prevent spam
-   Multiple notification channels (email, SMS, dashboard)
-   Configurable alert thresholds

#### `getInventoryReport(string $merchantId, array $filters = []): array`

**Purpose**: Generate comprehensive inventory report

**Parameters**:

-   `$merchantId` (string): Merchant UUID
-   `$filters` (array): Report filters
    -   `category_ids` (array, optional): Category filters
    -   `date_from` (\DateTime, optional): Date range start
    -   `date_to` (\DateTime, optional): Date range end
    -   `stock_status` (string, optional): 'all', 'low', 'out'

**Returns**: `array` - Inventory report data

```php
[
    'summary' => [
        'total_products' => 150,
        'in_stock' => 120,
        'low_stock' => 15,
        'out_of_stock' => 15,
        'total_value' => 2500000.00
    ],
    'products' => [...],
    'movements' => [...],
    'alerts' => [...]
]
```

#### `getStockMovementHistory(string $productId, \DateTime $startDate, \DateTime $endDate): Collection`

**Purpose**: Get stock movement history for product

**Parameters**:

-   `$productId` (string): Product UUID
-   `$startDate` (\DateTime): History start date
-   `$endDate` (\DateTime): History end date

**Returns**: `Collection` - Stock movements

```php
[
    [
        'date' => '2025-12-06T10:00:00Z',
        'type' => 'sale',
        'quantity' => -5,
        'reason' => 'Order #12345',
        'user' => 'customer@example.com'
    ]
]
```

#### `bulkUpdateStock(array $stockUpdates, string $merchantId): array`

**Purpose**: Bulk update stock for multiple products

**Parameters**:

-   `$stockUpdates` (array): Stock update data
    ```php
    [
        ['product_id' => 'uuid', 'quantity' => 50, 'reason' => 'restock'],
        ['variant_id' => 'uuid', 'quantity' => 25, 'reason' => 'adjustment']
    ]
    ```
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `array` - Update results with success/failure per item

**Business Rules**:

-   Transaction safety
-   Rollback on partial failure
-   Comprehensive logging

#### `importStockFromFile(string $filePath, string $merchantId): array`

**Purpose**: Import stock updates from file

**Parameters**:

-   `$filePath` (string): Path to import file
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `array` - Import results

```php
[
    'success' => true,
    'processed' => 150,
    'successful' => 145,
    'failed' => 5,
    'errors' => [...],
    'warnings' => [...]
]
```

**Supported Formats**:

-   CSV
-   Excel (XLSX)
-   JSON

**Business Rules**:

-   Async processing for large files
-   Data validation before import
-   Comprehensive error reporting

---

## 6. ProductPricingService

### Interface Definition

```php
interface IProductPricingService {
    public function getProductPricing(string $productId): array;
    public function updateProductBasePrice(string $productId, float $price, string $merchantId, string $reason = 'manual'): bool;
    public function updateVariantPrice(string $variantId, float $price, string $merchantId, string $reason = 'manual'): bool;
    public function calculateProductPrice(string $productId, array $modifiers = []): float;
    public function calculateVariantPrice(string $variantId, array $modifiers = []): float;
    public function applyDiscount(string $productId, float $discountPercent, string $merchantId, \DateTime $startDate = null, \DateTime $endDate = null): bool;
    public function bulkUpdatePrices(array $priceUpdates, string $merchantId): array;
    public function applyPriceMarkup(string $categoryId, float $markupPercent, string $merchantId): int;
    public function getPriceHistory(string $productId, \DateTime $startDate, \DateTime $endDate): Collection;
    public function getPriceChangeAnalytics(string $merchantId, \DateTime $startDate, \DateTime $endDate): array;
    public function calculatePriceWithTax(float $price, float $taxRate): float;
    public function convertCurrency(float $price, string $fromCurrency, string $toCurrency): float;
    public function validatePrice(float $price, array $rules = []): bool;
    public function checkPriceConsistency(string $productId): array;
}
```

### Method Specifications

#### `getProductPricing(string $productId): array`

**Purpose**: Get complete pricing structure for product

**Parameters**:

-   `$productId` (string): Product UUID

**Returns**: `array` - Complete pricing information

```php
[
    'product_id' => 'uuid',
    'base_price' => 50000.00,
    'current_price' => 45000.00,
    'discount_active' => true,
    'discount_percent' => 10.0,
    'discount_amount' => 5000.00,
    'variants' => [
        [
            'variant_id' => 'uuid',
            'price' => 55000.00,
            'discount_price' => 49500.00
        ]
    ],
    'tax_rate' => 11.0,
    'final_price' => 49950.00,
    'currency' => 'IDR'
]
```

#### `updateProductBasePrice(string $productId, float $price, string $merchantId, string $reason = 'manual'): bool`

**Purpose**: Update product's base price

**Parameters**:

-   `$productId` (string): Product UUID
-   `$price` (float): New base price
-   `$merchantId` (string): Merchant UUID for ownership validation
-   `$reason` (string): Price change reason

**Returns**: `bool` - Success status

**Business Rules**:

-   Price must be > 0
-   Logs price change history
-   Triggers cache invalidation
-   Updates search index

#### `updateVariantPrice(string $variantId, float $price, string $merchantId, string $reason = 'manual'): bool`

**Purpose**: Update variant's price

**Parameters**: Same as updateProductBasePrice

**Returns**: `bool` - Success status

**Business Rules**: Same as product price update

#### `calculateProductPrice(string $productId, array $modifiers = []): float`

**Purpose**: Calculate final product price with all modifiers

**Parameters**:

-   `$productId` (string): Product UUID
-   `$modifiers` (array): Price modifiers
    -   `discount_percent` (float, optional): Additional discount
    -   `tax_included` (bool, optional): Whether tax is included
    -   `quantity` (int, optional): Quantity for bulk pricing

**Returns**: `float` - Final calculated price

**Calculation Order**:

1. Base price
2. Apply discounts
3. Apply taxes
4. Apply quantity discounts

#### `calculateVariantPrice(string $variantId, array $modifiers = []): float`

**Purpose**: Calculate final variant price

**Parameters**: Same as calculateProductPrice

**Returns**: `float` - Final calculated price

#### `applyDiscount(string $productId, float $discountPercent, string $merchantId, \DateTime $startDate = null, \DateTime $endDate = null): bool`

**Purpose**: Apply percentage discount to product

**Parameters**:

-   `$productId` (string): Product UUID
-   `$discountPercent` (float): Discount percentage (0-100)
-   `$merchantId` (string): Merchant UUID for validation
-   `$startDate` (\DateTime, optional): Discount start date
-   `$endDate` (\DateTime, optional): Discount end date

**Returns**: `bool` - Success status

**Business Rules**:

-   Validates discount range
-   Applies to all variants proportionally
-   Logs discount application
-   Updates search index

#### `bulkUpdatePrices(array $priceUpdates, string $merchantId): array`

**Purpose**: Bulk update prices for multiple products/variants

**Parameters**:

-   `$priceUpdates` (array): Price update data
    ```php
    [
        ['type' => 'product', 'id' => 'uuid', 'price' => 100.00],
        ['type' => 'variant', 'id' => 'uuid', 'price' => 50.00]
    ]
    ```
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `array` - Update results with success/failure per item

#### `applyPriceMarkup(string $categoryId, float $markupPercent, string $merchantId): int`

**Purpose**: Apply markup to all products in category

**Parameters**:

-   `$categoryId` (string): Category UUID
-   `$markupPercent` (float): Markup percentage
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `int` - Number of products updated

**Business Rules**:

-   Applies to base prices only
-   Updates all variants proportionally
-   Comprehensive audit logging

#### `getPriceHistory(string $productId, \DateTime $startDate, \DateTime $endDate): Collection`

**Purpose**: Get price change history

**Parameters**:

-   `$productId` (string): Product UUID
-   `$startDate` (\DateTime): History start date
-   `$endDate` (\DateTime): History end date

**Returns**: `Collection` - Price change history

```php
[
    [
        'date' => '2025-12-06T10:00:00Z',
        'old_price' => 50000.00,
        'new_price' => 45000.00,
        'change_type' => 'discount',
        'reason' => 'Holiday promotion',
        'changed_by' => 'merchant@example.com'
    ]
]
```

#### `getPriceChangeAnalytics(string $merchantId, \DateTime $startDate, \DateTime $endDate): array`

**Purpose**: Get pricing analytics for merchant

**Parameters**:

-   `$merchantId` (string): Merchant UUID
-   `$startDate` (\DateTime): Analysis start date
-   `$endDate` (\DateTime): Analysis end date

**Returns**: `array` - Pricing analytics

```php
[
    'total_price_changes' => 245,
    'average_price_change' => 8.5,
    'price_volatility' => 0.15,
    'discount_usage' => 0.75,
    'most_changed_products' => [...],
    'price_trends' => [...]
]
```

#### `calculatePriceWithTax(float $price, float $taxRate): float`

**Purpose**: Calculate price including tax

**Parameters**:

-   `$price` (float): Base price
-   `$taxRate` (float): Tax rate percentage

**Returns**: `float` - Price with tax

**Formula**: `price * (1 + taxRate/100)`

#### `convertCurrency(float $price, string $fromCurrency, string $toCurrency): float`

**Purpose**: Convert price between currencies

**Parameters**:

-   `$price` (float): Price in from currency
-   `$fromCurrency` (string): Source currency code
-   `$toCurrency` (string): Target currency code

**Returns**: `float` - Converted price

**Business Rules**:

-   Uses real-time exchange rates
-   Caches rates for performance
-   Handles currency precision

#### `validatePrice(float $price, array $rules = []): bool`

**Purpose**: Validate price against business rules

**Parameters**:

-   `$price` (float): Price to validate
-   `$rules` (array): Additional validation rules
    -   `min_price` (float, optional): Minimum allowed price
    -   `max_price` (float, optional): Maximum allowed price
    -   `step_size` (float, optional): Price increment step

**Returns**: `bool` - Validation result

**Default Rules**:

-   Must be numeric
-   Must be >= 0
-   Maximum 2 decimal places

#### `checkPriceConsistency(string $productId): array`

**Purpose**: Check price consistency across variants

**Parameters**:

-   `$productId` (string): Product UUID

**Returns**: `array` - Consistency check results

```php
[
    'is_consistent' => true,
    'issues' => [],
    'recommendations' => [
        'Variant prices should be within 20% of base price'
    ]
]
```

**Checks Performed**:

-   Variant prices vs base price ratio
-   Discount consistency
-   Tax application consistency
-   Currency consistency

---

## 7. MerchantProductService

### Interface Definition

```php
interface IMerchantProductService {
    public function getMerchantProducts(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getMerchantProductById(string $productId, string $merchantId): ?Product;
    public function createMerchantProduct(array $productData, string $merchantId): Product;
    public function updateMerchantProduct(string $productId, array $productData, string $merchantId): Product;
    public function deleteMerchantProduct(string $productId, string $merchantId): bool;
    public function getMerchantDashboardStats(string $merchantId): array;
    public function getMerchantProductAnalytics(string $merchantId, \DateTime $startDate, \DateTime $endDate): array;
    public function bulkCreateProducts(array $productsData, string $merchantId): array;
    public function bulkUpdateMerchantProducts(array $updates, string $merchantId): array;
    public function bulkDeleteMerchantProducts(array $productIds, string $merchantId): array;
    public function validateMerchantOwnership(string $productId, string $merchantId): bool;
    public function checkMerchantLimits(string $merchantId): array;
    public function duplicateProductForMerchant(string $sourceProductId, string $targetMerchantId, string $requestingMerchantId): ?Product;
    public function transferProductOwnership(string $productId, string $fromMerchantId, string $toMerchantId): bool;
}
```

### Method Specifications

#### `getMerchantProducts(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator`

**Purpose**: Get paginated products for specific merchant

**Parameters**:

-   `$merchantId` (string): Merchant UUID
-   `$filters` (array): Filter criteria
    -   `category_id` (string, optional): Category filter
    -   `type` (string, optional): Product type filter
    -   `status` (string, optional): 'active', 'inactive'
    -   `search` (string, optional): Full-text search
    -   `has_variants` (bool, optional): Variant filter
    -   `price_min` (float, optional): Minimum price
    -   `price_max` (float, optional): Maximum price
-   `$perPage` (int): Items per page (default: 20)

**Returns**: `LengthAwarePaginator<Product>` - Paginated merchant products

**Security**: Strict ownership validation

#### `getMerchantProductById(string $productId, string $merchantId): ?Product`

**Purpose**: Get specific product with ownership validation

**Parameters**:

-   `$productId` (string): Product UUID
-   `$merchantId` (string): Merchant UUID for ownership check

**Returns**: `Product|null` - Product if owned by merchant, null otherwise

**Security**: Double ownership validation

#### `createMerchantProduct(array $productData, string $merchantId): Product`

**Purpose**: Create product for specific merchant

**Parameters**:

-   `$productData` (array): Product data (same as ProductManagementService.createProduct)
-   `$merchantId` (string): Merchant UUID (auto-assigned to data)

**Returns**: `Product` - Created product

**Business Rules**:

-   Auto-assigns merchant_id to product data
-   Validates merchant limits before creation
-   Checks SKU/barcode uniqueness per merchant

#### `updateMerchantProduct(string $productId, array $productData, string $merchantId): Product`

**Purpose**: Update merchant's product

**Parameters**:

-   `$productId` (string): Product UUID
-   `$productData` (array): Updated data
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `Product` - Updated product

**Security**: Ownership validation at multiple levels

#### `deleteMerchantProduct(string $productId, string $merchantId): bool`

**Purpose**: Delete merchant's product

**Parameters**:

-   `$productId` (string): Product UUID
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `bool` - Success status

**Business Rules**:

-   Additional checks for active orders
-   Validates no cross-merchant access

#### `getMerchantDashboardStats(string $merchantId): array`

**Purpose**: Get dashboard statistics for merchant

**Parameters**:

-   `$merchantId` (string): Merchant UUID

**Returns**: `array` - Dashboard statistics

```php
[
    'total_products' => 150,
    'active_products' => 120,
    'inactive_products' => 15,
    'products_with_variants' => 45,
    'total_variants' => 180,
    'low_stock_products' => 12,
    'out_of_stock_products' => 3,
    'total_sales_today' => 2500000.00,
    'total_orders_today' => 45,
    'average_rating' => 4.2,
    'total_reviews' => 1250,
    'top_selling_products' => [...],
    'recent_activities' => [...]
]
```

**Business Rules**:

-   Real-time calculations
-   Cached for performance
-   Merchant-specific data isolation

#### `getMerchantProductAnalytics(string $merchantId, \DateTime $startDate, \DateTime $endDate): array`

**Purpose**: Get detailed product analytics for merchant

**Parameters**:

-   `$merchantId` (string): Merchant UUID
-   `$startDate` (\DateTime): Analysis start date
-   `$endDate` (\DateTime): Analysis end date

**Returns**: `array` - Product analytics data

```php
[
    'sales_by_product' => [...],
    'sales_by_category' => [...],
    'revenue_trends' => [...],
    'top_performers' => [...],
    'underperformers' => [...],
    'inventory_turnover' => 4.2,
    'average_order_value' => 55000.00,
    'conversion_rate' => 0.15,
    'customer_retention' => 0.68
]
```

#### `bulkCreateProducts(array $productsData, string $merchantId): array`

**Purpose**: Create multiple products for merchant

**Parameters**:

-   `$productsData` (array): Array of product data arrays
-   `$merchantId` (string): Merchant UUID

**Returns**: `array` - Creation results

```php
[
    'successful' => 8,
    'failed' => 2,
    'results' => [
        ['index' => 0, 'success' => true, 'product_id' => 'uuid'],
        ['index' => 1, 'success' => false, 'errors' => ['SKU already exists']]
    ]
]
```

**Business Rules**:

-   Transaction safety
-   Rollback on validation failure
-   Progress tracking for large batches

#### `bulkUpdateMerchantProducts(array $updates, string $merchantId): array`

**Purpose**: Bulk update multiple merchant products

**Parameters**:

-   `$updates` (array): Update operations
    ```php
    [
        ['product_id' => 'uuid', 'data' => ['status' => 'active', 'price' => 100.00]],
        ['product_id' => 'uuid', 'data' => ['category_id' => 'new-category-uuid']]
    ]
    ```
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `array` - Update results with success/failure per product

#### `bulkDeleteMerchantProducts(array $productIds, string $merchantId): array`

**Purpose**: Bulk delete merchant products

**Parameters**:

-   `$productIds` (array): Array of product UUIDs
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `array` - Deletion results

```php
[
    'successful' => 5,
    'failed' => 1,
    'results' => [
        ['product_id' => 'uuid', 'success' => true],
        ['product_id' => 'uuid', 'success' => false, 'reason' => 'Has active orders']
    ]
]
```

#### `validateMerchantOwnership(string $productId, string $merchantId): bool`

**Purpose**: Validate merchant ownership of product

**Parameters**:

-   `$productId` (string): Product UUID
-   `$merchantId` (string): Merchant UUID

**Returns**: `bool` - Ownership validation result

**Security**: Critical security method used throughout service

#### `checkMerchantLimits(string $merchantId): array`

**Purpose**: Check merchant's product limits and quotas

**Parameters**:

-   `$merchantId` (string): Merchant UUID

**Returns**: `array` - Limit check results

```php
[
    'can_create_more' => true,
    'current_count' => 150,
    'max_allowed' => 500,
    'remaining_slots' => 350,
    'limits' => [
        'max_products' => 500,
        'max_variants_per_product' => 50,
        'max_categories' => 20
    ]
]
```

#### `duplicateProductForMerchant(string $sourceProductId, string $targetMerchantId, string $requestingMerchantId): ?Product`

**Purpose**: Duplicate product for another merchant

**Parameters**:

-   `$sourceProductId` (string): Source product UUID
-   `$targetMerchantId` (string): Target merchant UUID
-   `$requestingMerchantId` (string): Requesting merchant UUID

**Returns**: `Product|null` - Duplicated product or null

**Business Rules**:

-   Validates requesting merchant permissions
-   Generates new unique identifiers
-   Copies product structure but not sales data

#### `transferProductOwnership(string $productId, string $fromMerchantId, string $toMerchantId): bool`

**Purpose**: Transfer product ownership between merchants

**Parameters**:

-   `$productId` (string): Product UUID
-   `$fromMerchantId` (string): Current owner merchant UUID
-   `$toMerchantId` (string): New owner merchant UUID

**Returns**: `bool` - Transfer success status

**Business Rules**:

-   Validates transfer permissions
-   Updates all related records
-   Comprehensive audit logging
-   Handles category and attribute ownership

---

## 8. ProductAuthorizationService

### Interface Definition

```php
interface IProductAuthorizationService {
    public function canAccessProduct(string $userId, string $productId, string $permission = 'view'): bool;
    public function canModifyProduct(string $userId, string $productId): bool;
    public function canDeleteProduct(string $userId, string $productId): bool;
    public function isProductOwner(string $merchantId, string $productId): bool;
    public function canCreateProduct(string $merchantId): bool;
    public function validateMerchantPermissions(string $merchantId, array $permissions): array;
    public function hasRole(string $userId, string $role): bool;
    public function hasPermission(string $userId, string $permission): bool;
    public function getUserPermissions(string $userId): array;
    public function canAccessMerchantData(string $userId, string $merchantId): bool;
    public function validateCrossMerchantAccess(string $requestingMerchantId, string $targetMerchantId): bool;
    public function canAdminAccess(string $adminId): bool;
    public function validateAdminPermissions(string $adminId, array $permissions): array;
    public function logAccessAttempt(string $userId, string $resourceId, string $action, bool $granted): void;
    public function getAccessLogs(string $resourceId, \DateTime $startDate, \DateTime $endDate): Collection;
}
```

### Method Specifications

#### `canAccessProduct(string $userId, string $productId, string $permission = 'view'): bool`

**Purpose**: Check if user can access product with specific permission

**Parameters**:

-   `$userId` (string): User UUID
-   `$productId` (string): Product UUID
-   `$permission` (string): Permission type ('view', 'edit', 'delete', 'manage')

**Returns**: `bool` - Access granted status

**Permission Levels**:

-   `view`: Can see product details
-   `edit`: Can modify product
-   `delete`: Can delete product
-   `manage`: Full management rights

#### `canModifyProduct(string $userId, string $productId): bool`

**Purpose**: Check if user can modify product

**Parameters**:

-   `$userId` (string): User UUID
-   `$productId` (string): Product UUID

**Returns**: `bool` - Modification permission status

**Business Rules**:

-   Checks ownership or admin rights
-   Validates user status (active, not suspended)

#### `canDeleteProduct(string $userId, string $productId): bool`

**Purpose**: Check if user can delete product

**Parameters**:

-   `$userId` (string): User UUID
-   `$productId` (string): Product UUID

**Returns**: `bool` - Deletion permission status

**Business Rules**:

-   Stricter than modify permissions
-   Additional checks for active orders/references

#### `isProductOwner(string $merchantId, string $productId): bool`

**Purpose**: Check if merchant owns the product

**Parameters**:

-   `$merchantId` (string): Merchant UUID
-   `$productId` (string): Product UUID

**Returns**: `bool` - Ownership status

**Security**: Core ownership validation method

#### `canCreateProduct(string $merchantId): bool`

**Purpose**: Check if merchant can create new products

**Parameters**:

-   `$merchantId` (string): Merchant UUID

**Returns**: `bool` - Creation permission status

**Business Rules**:

-   Checks merchant status (active, verified)
-   Validates product limits/quota
-   Checks subscription plan permissions

#### `validateMerchantPermissions(string $merchantId, array $permissions): array`

**Purpose**: Validate multiple permissions for merchant

**Parameters**:

-   `$merchantId` (string): Merchant UUID
-   `$permissions` (array): Array of permission strings

**Returns**: `array` - Permission validation results

```php
[
    'all_granted' => false,
    'granted' => ['create_product', 'view_analytics'],
    'denied' => ['delete_product', 'manage_users'],
    'reasons' => [
        'delete_product' => 'Merchant subscription does not allow deletion'
    ]
]
```

#### `hasRole(string $userId, string $role): bool`

**Purpose**: Check if user has specific role

**Parameters**:

-   `$userId` (string): User UUID
-   `$role` (string): Role name ('merchant', 'admin', 'super_admin')

**Returns**: `bool` - Role possession status

#### `hasPermission(string $userId, string $permission): bool`

**Purpose**: Check if user has specific permission

**Parameters**:

-   `$userId` (string): User UUID
-   `$permission` (string): Permission string

**Returns**: `bool` - Permission possession status

**Permission Examples**:

-   `products.create`
-   `products.edit.own`
-   `products.delete.any`
-   `analytics.view`

#### `getUserPermissions(string $userId): array`

**Purpose**: Get all permissions for user

**Parameters**:

-   `$userId` (string): User UUID

**Returns**: `array` - User permissions array

```php
[
    'roles' => ['merchant'],
    'permissions' => [
        'products.create',
        'products.edit.own',
        'products.view.own',
        'analytics.view.own'
    ],
    'restrictions' => [
        'max_products' => 100,
        'max_categories' => 10
    ]
]
```

#### `canAccessMerchantData(string $userId, string $merchantId): bool`

**Purpose**: Check if user can access merchant's data

**Parameters**:

-   `$userId` (string): User UUID
-   `$merchantId` (string): Merchant UUID

**Returns**: `bool` - Data access permission status

**Business Rules**:

-   User must be merchant owner or admin
-   Validates merchant-user relationship

#### `validateCrossMerchantAccess(string $requestingMerchantId, string $targetMerchantId): bool`

**Purpose**: Validate cross-merchant access permissions

**Parameters**:

-   `$requestingMerchantId` (string): Requesting merchant UUID
-   `$targetMerchantId` (string): Target merchant UUID

**Returns**: `bool` - Cross-merchant access allowed

**Business Rules**:

-   Prevents unauthorized data access
-   Used for product duplication/sharing features

#### `canAdminAccess(string $adminId): bool`

**Purpose**: Check if user has admin access

**Parameters**:

-   `$adminId` (string): Admin user UUID

**Returns**: `bool` - Admin access status

**Business Rules**:

-   Validates admin role and status
-   Checks admin permissions scope

#### `validateAdminPermissions(string $adminId, array $permissions): array`

**Purpose**: Validate admin permissions

**Parameters**:

-   `$adminId` (string): Admin user UUID
-   `$permissions` (array): Required permissions

**Returns**: `array` - Permission validation results

**Business Rules**:

-   Checks admin role hierarchy
-   Validates permission scope (global, regional, etc.)

#### `logAccessAttempt(string $userId, string $resourceId, string $action, bool $granted): void`

**Purpose**: Log access attempts for audit trail

**Parameters**:

-   `$userId` (string): User UUID attempting access
-   `$resourceId` (string): Resource UUID being accessed
-   `$action` (string): Action attempted ('view', 'edit', 'delete')
-   `$granted` (bool): Whether access was granted

**Returns**: `void`

**Business Rules**:

-   Async logging for performance
-   Comprehensive audit trail
-   Security monitoring integration

#### `getAccessLogs(string $resourceId, \DateTime $startDate, \DateTime $endDate): Collection`

**Purpose**: Get access logs for resource

**Parameters**:

-   `$resourceId` (string): Resource UUID
-   `$startDate` (\DateTime): Log start date
-   `$endDate` (\DateTime): Log end date

**Returns**: `Collection` - Access log entries

```php
[
    [
        'timestamp' => '2025-12-06T10:00:00Z',
        'user_id' => 'uuid',
        'user_type' => 'merchant',
        'action' => 'edit',
        'granted' => true,
        'ip_address' => '192.168.1.1',
        'user_agent' => 'Mozilla/5.0...'
    ]
]
```

**Business Rules**:

-   Admin-only access
-   Comprehensive audit trail
-   GDPR compliance considerations

---

## Implementation Notes

### Service Layer Architecture:

-   **Dependency Injection**: All services use constructor injection for repositories
-   **Interface Segregation**: Each service has its own interface
-   **Single Responsibility**: Each service handles one domain area
-   **Validation**: Business rule validation in service layer
-   **Caching**: Strategic caching for performance
-   **Logging**: Comprehensive audit logging
-   **Error Handling**: Proper exception handling and user feedback

### Common Patterns:

-   **Ownership Validation**: Every merchant operation validates ownership
-   **Soft Deletes**: Respect soft delete constraints
-   **Version Control**: Track changes with version numbers
-   **Bulk Operations**: Support for mass operations with rollback
-   **Analytics**: Built-in analytics and reporting
-   **Caching Strategy**: TTL-based caching with smart invalidation

### Security Considerations:

-   **Authorization**: Every endpoint validates permissions
-   **Data Isolation**: Merchant data isolation enforced
-   **Input Validation**: Comprehensive validation at service level
-   **Audit Trail**: All operations logged for compliance

### Performance Optimizations:

-   **Database Indexing**: Optimized queries with proper indexes
-   **Caching**: Multi-level caching strategy
-   **Pagination**: Efficient pagination for large datasets
-   **Async Processing**: Background jobs for heavy operations
-   **Query Optimization**: N+1 problem prevention

This specification provides a comprehensive blueprint for implementing a production-ready product management system with proper separation of concerns, security, and performance considerations.
