# Product Module Service Specifications

## Overview

This document contains detailed specifications for all services in the Product module, focused on merchant product management operations. User-facing product access (search, browsing) is handled by the separate Catalog module. Each service includes interface definitions, method signatures, and detailed descriptions of functionality.

## 1. Architecture Overview

### Layer Architecture

The Product module follows Clean Architecture principles with the following layers:

```
┌─────────────────────────────────────┐
│         HTTP Controllers            │
│         (API Endpoints)             │
├─────────────────────────────────────┤
│         Use Cases                   │
│         (Business Logic)            │
├─────────────────────────────────────┤
│         Services                    │
│         (Domain Logic)              │
├─────────────────────────────────────┤
│         Repositories                │
│         (Data Access)               │
├─────────────────────────────────────┤
│         Models & Database           │
└─────────────────────────────────────┘
```

### Key Principles

-   **Use Cases use Services**: Use cases orchestrate business logic and delegate to services
-   **Services use Repositories**: Services handle domain logic and data persistence
-   **Dependency Injection**: All dependencies are injected through constructors
-   **Interface Segregation**: Each service has its own interface

### Folder Structure

```
Modules/Product/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── UseCases/
│   │   ├── Category/
│   │   ├── Product/
│   │   ├── Variant/
│   │   └── Pricing/
│   ├── Services/
│   │   ├── Category/
│   │   ├── Product/
│   │   ├── Variant/
│   │   └── Pricing/
│   ├── Repositories/
│   │   ├── Category/
│   │   ├── Product/
│   │   ├── Variant/
│   │   └── Pricing/
│   └── Models/
├── database/
└── routes/
```

## 2. Middleware

### Authentication Middleware

-   **Purpose**: Validates JWT tokens and user authentication
-   **Location**: `Modules/Product/app/Http/Middleware/AuthenticationMiddleware`
-   **Note**: Similar to `Modules/Authentication/app/Http/Middleware/JWTMiddleware` but implemented within Product module
-   **Applied to**: All product management endpoints

### Authorization Middleware

-   **Purpose**: Validates user permissions and roles
-   **Location**: `Modules/Product/app/Http/Middleware/AuthorizationMiddleware`
-   **Note**: Similar to `Modules/Authorization/app/Http/Middleware/AuthorizationMiddleware` but implemented within Product module
-   **Applied to**: All product management endpoints

### Merchant Access Middleware

-   **Purpose**: Identifies and validates the selected merchant context
-   **Functionality**:
    -   Extracts merchant ID from request headers or route parameters
    -   Validates merchant exists and is active
    -   Sets merchant context for the request
    -   Ensures user has access to the merchant
-   **Applied to**: All merchant-specific endpoints

### Middleware Stack Order

```php
// Applied in this order for product endpoints
'middleware' => [
    'auth.jwt',           // Authentication
    'auth.authorization', // Authorization
    'merchant.access',    // Merchant context
    // ... other middleware
]
```

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [Middleware](#2-middleware)
3. [CategoryManagementService](#3-categorymanagementservice)
4. [ProductManagementService](#4-productmanagementservice)
5. [ProductVariantManagementService](#5-productvariantmanagementservice)
6. [ProductPricingService](#6-productpricingservice)
7. [AttributeManagementService](#7-attributemanagementservice)
8. [ProductExtrasManagementService](#8-productextrasmanagementservice)
9. [ProductBundlesManagementService](#9-productbundlesmanagementservice)
10. [UnitConversionManagementService](#10-unitconversionmanagementservice)
11. [ProductServiceDetailsManagementService](#11-productserviceDetailsmanagementservice)
12. [MerchantProductService](#12-merchantproductservice)
13. [Use Cases](#13-use-cases)
14. [Elasticsearch Integration](#14-elasticsearch-integration) - Search index management and data synchronization
15. [Business Logic Services](business_logic_services.md) - AI-powered intelligence and automation services

---

## 3. CategoryManagementService

### Overview

The CategoryManagementService handles all category-related business operations with strict hierarchy management and business rule enforcement. It ensures category integrity, prevents circular references, and manages category relationships with business context. Categories are global/system-wide and not tied to specific merchants.

### Interface Definition

```php
interface ICategoryManagementService {
    // Core CRUD Operations with Business Logic
    public function createCategory(array $categoryData): Category;
    public function updateCategory(string $categoryId, array $categoryData): Category;
    public function deleteCategory(string $categoryId): bool;
    public function getCategoryById(string $categoryId): ?Category;
    public function getCategoryWithHierarchy(string $categoryId): ?Category;

    // Advanced Retrieval with Business Context
    public function getCategories(array $filters = []): Collection;
    public function getCategoryTree(): Collection;
    public function getCategoryPath(string $categoryId): Collection;

    // Business Logic Operations
    public function moveCategory(string $categoryId, ?string $newParentId): bool;
    public function bulkUpdateCategories(array $categoryIds, array $updateData): array;

    // Business Validation & Rules
    public function validateCategoryData(array $categoryData): array;
    public function validateCategoryHierarchy(string $categoryId, ?string $parentId): array;
    public function checkCategoryConstraints(string $categoryId): array;

    // Business Rules Enforcement
    public function enforceCategoryLimits(): array;
    public function validateCategoryUniqueness(array $uniquenessData): array;
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

#### `createCategory(array $categoryData): Category`

**Purpose**: Create new category with validation

**Parameters**:

-   `$categoryData` (array): Category data
    -   `name` (string, required): Category name
    -   `slug` (string, optional): URL slug, auto-generated if not provided
    -   `description` (string, optional): Category description
    -   `parent_id` (string, optional): Parent category UUID

**Returns**: `Category` - Created category model

**Validations**:

-   Name: required, max 255 characters
-   Slug: unique, auto-generated from name
-   Parent ID: must exist if provided

**Business Rules**:

-   Auto-generates unique slug
-   Sets initial level based on parent
-   Updates category path

#### `updateCategory(string $categoryId, array $categoryData): Category`

**Purpose**: Update existing category

**Parameters**:

-   `$categoryId` (string): Category UUID
-   `$categoryData` (array): Updated category data (same structure as create)

**Returns**: `Category` - Updated category model

**Business Rules**:

-   Regenerates slug if name changed
-   Updates path if parent changed
-   Updates descendant paths if hierarchy changed

#### `deleteCategory(string $categoryId): bool`

**Purpose**: Soft delete category

**Parameters**:

-   `$categoryId` (string): Category UUID

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

#### `moveCategory(string $categoryId, ?string $newParentId): bool`

**Purpose**: Move category to new parent in hierarchy

**Parameters**:

-   `$categoryId` (string): Category UUID to move
-   `$newParentId` (string|null): New parent UUID or null for root

**Returns**: `bool` - Success status

**Business Rules**:

-   Validates no circular references
-   Updates category level
-   Updates paths for category and all descendants

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

## 4. ProductManagementService

### Overview

The ProductManagementService handles all product management business logic with proper validation, merchant isolation, and business rule enforcement. It coordinates with repositories while adding business value through validation, constraints, and workflow management.

### Interface Definition

```php
interface IProductManagementService {
    // Core CRUD Operations with Business Logic
    public function createProduct(array $productData, string $merchantId): Product;
    public function updateProduct(string $productId, array $productData, string $merchantId): Product;
    public function deleteProduct(string $productId, string $merchantId): bool;
    public function getProductById(string $productId, string $merchantId): ?Product;
    public function getProductWithRelations(string $productId, string $merchantId): ?Product;

    // Advanced Retrieval with Business Context
    public function getMerchantProducts(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function searchProducts(array $searchCriteria, int $perPage = 20): LengthAwarePaginator;

    // Business Logic Operations
    public function duplicateProduct(string $productId, string $merchantId, array $overrides = []): Product;
    public function bulkUpdateProducts(array $productIds, array $updateData, string $merchantId): array;
    public function toggleProductStatus(string $productId, bool $active, string $merchantId): bool;

    // Business Validation & Rules
    public function validateProductData(array $productData, string $merchantId): array;
    public function validateProductUpdate(string $productId, array $updateData, string $merchantId): array;
    public function checkProductConstraints(string $productId, string $merchantId): array;

    // Business Rules Enforcement
    public function enforceProductLimits(string $merchantId): array;

    // Utility Methods with Business Logic
    public function generateProductSlug(string $name, ?string $excludeId = null): string;
    public function updateProductVersion(string $productId): bool;
    public function validateProductUniqueness(array $uniquenessData, string $merchantId): array;
}
```

### Parameter and Return Type Definitions

#### Product Data Structures

**Create/Update Product Data:**

```php
[
    'name' => 'string (required, max:255)',
    'description' => 'string|null (optional)',
    'type' => 'ProductTypeEnum (required: food|mart|service)',
    'category_id' => 'string|null (optional, must exist)',
    'price' => 'float|null (optional, >= 0)',
    'sku' => 'string|null (optional, unique per merchant)',
    'barcode' => 'string|null (optional, unique per merchant)',
    'has_variant' => 'bool (optional, default: false)',
    'metadata' => 'array|null (optional, custom fields)',
    'status' => 'ProductStatusEnum (optional: active|inactive|draft)'
]
```

**Filter Criteria:**

```php
[
    'category_id' => 'string|null',
    'type' => 'ProductTypeEnum|null',
    'status' => 'ProductStatusEnum|null',
    'search' => 'string|null (full-text search)',
    'price_min' => 'float|null',
    'price_max' => 'float|null',
    'has_variant' => 'bool|null',
    'date_from' => 'string|null (Y-m-d)',
    'date_to' => 'string|null (Y-m-d)'
]
```

**Validation Result:**

```php
[
    'is_valid' => 'bool',
    'errors' => 'array (field => message[])',
    'warnings' => 'array (field => message[])',
    'business_rules' => 'array (rule_name => status)'
]
```

### Method Specifications

#### `createProduct(array $productData, string $merchantId): Product`

**Purpose**: Creates a new product with comprehensive business validation and merchant-specific rules

**Parameters**:

-   `$productData` (array): Product creation data with clear structure (see Parameter Definitions above)
-   `$merchantId` (string): Merchant UUID for ownership validation

**Returns**: `Product` - Created product model instance

**Business Logic**:

-   Validates merchant ownership and product creation limits
-   Enforces SKU/barcode uniqueness within merchant scope
-   Auto-generates SEO-friendly slug from product name
-   Validates category exists and belongs to merchant
-   Applies product type-specific validation rules
-   Sets default values and business constraints
-   Increments product version for change tracking
-   Triggers audit logging and business events

**Business Rules Applied**:

-   Merchant product limits based on subscription tier
-   Product naming conventions and character restrictions
-   Category hierarchy and merchant ownership validation
-   Pricing validation (must be >= 0, proper decimal places)
-   SKU/barcode uniqueness per merchant
-   Product type constraints and metadata requirements

#### `getMerchantProducts(GetMerchantProductsQuery $query): PaginatedProductResult`

**Purpose**: Retrieves products for a specific merchant with business-aware filtering and pagination

**Parameters**:

-   `$query` (GetMerchantProductsQuery): Structured query with merchant context and filtering options

**Returns**: `PaginatedProductResult` - Contains paginated products with business metadata

**Business Logic**:

-   Enforces strict merchant data isolation
-   Applies role-based visibility rules (merchant vs admin)
-   Includes real-time availability status
-   Calculates dynamic pricing based on active promotions
-   Filters out expired or discontinued products based on business rules
-   Applies category hierarchy filtering with inheritance
-   Includes performance metrics for each product

**Business Rules Applied**:

-   Merchant ownership validation at query level
-   Product visibility based on publication status and business rules
-   Dynamic pricing calculation including taxes and discounts
-   Category-based access control
-   Performance data aggregation (views, orders, ratings)

#### `validateProductCreation(CreateProductCommand $command): ValidationResult`

**Purpose**: Performs comprehensive business validation for product creation

**Parameters**:

-   `$command` (CreateProductCommand): Product creation data to validate

**Returns**: `ValidationResult` - Detailed validation results with business rule violations

**Business Validation Rules**:

-   **Merchant Limits**: Checks against merchant's product creation limits
-   **Uniqueness**: SKU/barcode uniqueness within merchant scope
-   **Category Validation**: Ensures category exists and is active for merchant
-   **Naming Conventions**: Validates product names against business rules
-   **Pricing Rules**: Validates price ranges and decimal precision
-   **Type Constraints**: Product type-specific validation rules
-   **SEO Compliance**: Slug generation and uniqueness validation
-   **Metadata Validation**: Custom field validation based on product type

**Cross-cutting Concerns**:

-   Audit logging of validation attempts
-   Business rule violation tracking
-   Warning generation for non-blocking issues

#### `updateProduct(UpdateProductCommand $command): ProductResult`

**Purpose**: Updates an existing product with comprehensive business rule enforcement and audit trail

**Parameters**:

-   `$command` (UpdateProductCommand): Structured update command with validation

**Returns**: `ProductResult` - Update result with business context

**Business Logic**:

-   Validates update permissions based on merchant ownership
-   Enforces field-level update restrictions
-   Manages product version increment for optimistic locking
-   Triggers business rule re-evaluation
-   Updates related entities (variants, pricing) as needed
-   Generates audit trail with change reasons
-   Handles category changes with hierarchy validation
-   Manages SEO slug updates with conflict resolution

**Business Rules Applied**:

-   **Version Control**: Increments version number for change tracking
-   **Ownership Validation**: Ensures merchant owns the product
-   **Field Restrictions**: Certain fields cannot be updated after publication
-   **Dependency Checks**: Validates updates don't break related entities
-   **Audit Requirements**: All changes logged with business justification

#### `duplicateProduct(DuplicateProductCommand $command): ProductResult`

**Purpose**: Creates a business-compliant duplicate of an existing product

**Parameters**:

-   `$command` (DuplicateProductCommand): Duplication parameters with business rules

**Returns**: `ProductResult` - Result containing the new duplicated product

**Business Logic**:

-   Validates source product ownership and duplication permissions
-   Generates new unique identifiers (SKU, barcode, slug)
-   Applies duplication business rules (what to copy vs reset)
-   Handles variant duplication with attribute uniqueness
-   Resets business-relevant fields (version, timestamps)
-   Maintains category relationships
-   Generates audit trail for duplication action

**Business Rules Applied**:

-   **Selective Copying**: Only business-appropriate data is duplicated
-   **Uniqueness Generation**: New unique identifiers generated
-   **Ownership Transfer**: Duplicate belongs to same merchant
-   **Version Reset**: New product starts with version 1
-   **Relationship Handling**: Variants and extras duplicated appropriately

#### `enforceProductLimits(string $merchantId): LimitEnforcementResult`

**Purpose**: Enforces merchant-specific product limits and business constraints

**Parameters**:

-   `$merchantId` (string): Merchant UUID to check limits for

**Returns**: `LimitEnforcementResult` - Current limits and enforcement status

**Business Logic**:

-   Retrieves merchant's subscription tier and limits
-   Calculates current product counts by type and status
-   Determines available capacity for new products
-   Applies business rules for limit enforcement
-   Provides recommendations for limit increases
-   Logs limit check activities for compliance

**Business Rules Applied**:

-   **Tier-based Limits**: Different limits per subscription tier
-   **Type-specific Quotas**: Separate limits for different product types
-   **Time-based Limits**: Rate limiting for product creation
-   **Soft vs Hard Limits**: Warnings vs blocking enforcement

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

---

## Business Rules & Validation

### Core Business Rules

The ProductManagementService enforces comprehensive business rules across all operations:

#### 1. Merchant Ownership & Isolation

-   **Strict Ownership**: All operations validate merchant ownership
-   **Data Isolation**: Merchants can only access their own products
-   **Cross-merchant Operations**: Explicit permissions required for transfers

#### 2. Product Lifecycle Management

-   **Creation Limits**: Enforced based on merchant subscription tier
-   **Publication Workflow**: Draft → Review → Published states
-   **Expiration Handling**: Automatic deactivation based on business rules
-   **Version Control**: Optimistic locking with conflict resolution

#### 3. Data Integrity & Uniqueness

-   **SKU Uniqueness**: Per-merchant SKU uniqueness enforcement
-   **Barcode Standards**: Validation against industry standards
-   **Slug Generation**: SEO-friendly, unique URL slugs
-   **Naming Conventions**: Business-specific naming rules

#### 4. Pricing & Commerce Rules

-   **Price Validation**: Minimum/maximum price constraints
-   **Currency Handling**: Multi-currency support with conversion
-   **Tax Compliance**: Automatic tax calculation and application
-   **Discount Rules**: Business logic for discount application

#### 5. Category & Classification

-   **Hierarchy Enforcement**: Category hierarchy integrity
-   **Classification Rules**: Product type-specific categorization
-   **Cross-category Validation**: Business rules for category changes

### Validation Approach

#### Service-Level Validation

Each service method performs validation appropriate to its business context:

```php
public function createProduct(array $productData, string $merchantId): Product
{
    // 1. Basic input validation
    $this->validateProductData($productData, $merchantId);

    // 2. Business rule validation
    $this->validateProductConstraints($productData, $merchantId);

    // 3. Merchant limit checks
    $this->enforceProductLimits($merchantId);

    // 4. Create product via repository
    $product = $this->productRepository->create($productData);

    // 5. Post-creation business logic
    $this->updateProductVersion($product->id);
    $this->triggerProductCreatedEvents($product);

    return $product;
}
```

#### Validation Result Structure

```php
[
    'is_valid' => bool,
    'errors' => ['field' => ['error message']],
    'warnings' => ['field' => ['warning message']],
    'business_rules' => ['rule_name' => 'status']
]
```

### Cross-cutting Concerns

#### Audit Trail

-   All business operations logged with context
-   Change reasons required for sensitive operations
-   Compliance reporting capabilities
-   Historical change tracking

#### Event Dispatching

-   Laravel events for significant business operations
-   Asynchronous processing for performance
-   Integration with external systems (Elasticsearch, analytics)

#### Performance Optimization

-   Strategic caching with business-aware invalidation
-   Query optimization for complex business rules
-   Background processing for heavy validations

#### Error Handling

-   Business-specific exceptions with context
-   Graceful degradation for non-critical failures
-   Comprehensive error logging and monitoring

## 5. ProductVariantManagementService

### Overview

The ProductVariantManagementService manages product variants with complex attribute combinations, pricing strategies, and inventory constraints. It enforces variant uniqueness rules, manages attribute relationships, and handles bulk variant operations with business validation.

### Interface Definition

```php
interface IProductVariantManagementService {
    // Core CRUD Operations with Business Logic
    public function createVariant(array $variantData, string $productId, string $merchantId): ProductVariant;
    public function updateVariant(string $variantId, array $variantData, string $merchantId): ProductVariant;
    public function deleteVariant(string $variantId, string $merchantId): bool;
    public function getVariantById(string $variantId, string $merchantId): ?ProductVariant;

    // Advanced Retrieval with Business Context
    public function getProductVariants(string $productId, string $merchantId): Collection;
    public function getVariantCombinations(string $productId, string $merchantId): Collection;

    // Business Logic Operations
    public function updateVariantPrice(string $variantId, float $price, string $merchantId): bool;
    public function bulkUpdateVariantPrices(array $variantIds, float $priceAdjustment, string $merchantId): array;

    // Attribute & Combination Management
    public function validateVariantData(array $variantData, string $productId, string $merchantId): array;
    public function generateVariantSku(string $productSku, array $attributes): string;
    public function checkAttributeCombinationExists(array $attributes, string $productId, ?string $excludeId = null): bool;

    // Business Rules Enforcement
    public function validateVariantConstraints(string $variantId, string $merchantId): array;
    public function enforceVariantLimits(string $merchantId): array;
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

## 6. ProductPricingService

### Overview

The ProductPricingService manages all pricing-related business logic including dynamic pricing, discount strategies, tax calculations, currency conversions, and price consistency validation. It enforces pricing business rules and provides comprehensive pricing analytics.

### Interface Definition

```php
interface IProductPricingService {
    // Core Pricing Operations
    public function updateProductBasePrice(string $productId, float $price, string $merchantId, string $reason = 'manual'): bool;
    public function updateVariantPrice(string $variantId, float $price, string $merchantId, string $reason = 'manual'): bool;
    public function applyDiscount(string $productId, float $discountPercent, string $merchantId, \DateTime $startDate = null, \DateTime $endDate = null): bool;

    // Price Calculation & Retrieval
    public function calculateProductPrice(string $productId, array $modifiers = []): float;
    public function calculateVariantPrice(string $variantId, array $modifiers = []): float;
    public function getProductPricing(string $productId): array;
    public function getPriceHistory(string $productId, \DateTime $startDate, \DateTime $endDate): Collection;

    // Business Logic Operations
    public function bulkUpdatePrices(array $priceUpdates, string $merchantId): array;
    public function applyPriceMarkup(string $categoryId, float $markupPercent, string $merchantId): int;

    // Tax & Currency Management
    public function calculatePriceWithTax(float $price, float $taxRate): float;
    public function convertCurrency(float $price, string $fromCurrency, string $toCurrency): float;

    // Validation
    public function validatePrice(float $price, array $rules = []): bool;
    public function checkPriceConsistency(string $productId): array;

    // Business Rules Enforcement
    public function enforcePricingLimits(string $merchantId): array;
    public function validatePriceConstraints(float $price, array $rules = []): array;
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

## 12. MerchantProductService

### Overview

The MerchantProductService provides merchant-specific product operations with enhanced security, business rule enforcement, and merchant-centric features. It acts as a facade over other product services while adding merchant-specific business logic and constraints.

### Interface Definition

```php
interface IMerchantProductService {
    // Core Merchant Product Operations
    public function createMerchantProduct(array $productData, string $merchantId): Product;
    public function updateMerchantProduct(string $productId, array $productData, string $merchantId): Product;
    public function deleteMerchantProduct(string $productId, string $merchantId): bool;
    public function transferProductOwnership(string $productId, string $fromMerchantId, string $toMerchantId): bool;

    // Merchant-Specific Retrieval
    public function getMerchantProducts(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getMerchantProductById(string $productId, string $merchantId): ?Product;
    public function getMerchantDashboardStats(string $merchantId): array;

    // Bulk Operations with Business Logic
    public function bulkCreateProducts(array $productsData, string $merchantId): array;
    public function bulkUpdateMerchantProducts(array $updates, string $merchantId): array;
    public function bulkDeleteMerchantProducts(array $productIds, string $merchantId): array;

    // Business Logic & Validation
    public function validateMerchantOwnership(string $productId, string $merchantId): bool;
    public function checkMerchantLimits(string $merchantId): array;
    public function duplicateProductForMerchant(string $sourceProductId, string $targetMerchantId, string $requestingMerchantId): ?Product;

    // Business Rules Enforcement
    public function enforceMerchantConstraints(string $merchantId): array;
    public function validateMerchantPermissions(string $merchantId, string $permission): bool;

    // Business Rules Enforcement
    public function enforceMerchantConstraints(string $merchantId): array;
    public function validateMerchantPermissions(string $merchantId, string $permission): bool;
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

## 7. AttributeManagementService

### Overview

The AttributeManagementService manages both master attributes (system-defined) and custom attributes (merchant-defined) for product variants. It ensures attribute consistency, uniqueness, and proper relationships with products and variants.

### Interface Definition

```php
interface IAttributeManagementService {
    // Master Attribute Operations
    public function createMasterAttribute(array $attributeData): AttributeMaster;
    public function updateMasterAttribute(string $attributeId, array $attributeData): AttributeMaster;
    public function deleteMasterAttribute(string $attributeId): bool;
    public function getMasterAttributeById(string $attributeId): ?AttributeMaster;
    public function getMasterAttributes(array $filters = []): Collection;

    // Custom Attribute Operations
    public function createCustomAttribute(array $attributeData, string $merchantId): AttributeCustom;
    public function updateCustomAttribute(string $attributeId, array $attributeData, string $merchantId): AttributeCustom;
    public function deleteCustomAttribute(string $attributeId, string $merchantId): bool;
    public function getCustomAttributeById(string $attributeId, string $merchantId): ?AttributeCustom;
    public function getMerchantCustomAttributes(string $merchantId, array $filters = []): Collection;

    // Business Logic Operations
    public function validateAttributeUniqueness(string $key, ?string $excludeId = null, ?string $merchantId = null): bool;
    public function getAttributeCombinations(string $productId): array;
    public function migrateCustomToMaster(string $customAttributeId, string $merchantId): ?AttributeMaster;
}
```

---

## 8. ProductExtrasManagementService

### Overview

The ProductExtrasManagementService manages additional items that can be added to products (like toppings, add-ons, customizations). It handles pricing, availability, and merchant-specific extras configuration.

### Interface Definition

```php
interface IProductExtrasManagementService {
    // Core CRUD Operations
    public function createProductExtra(array $extraData, string $productId, string $merchantId): ProductExtra;
    public function updateProductExtra(string $extraId, array $extraData, string $merchantId): ProductExtra;
    public function deleteProductExtra(string $extraId, string $merchantId): bool;
    public function getProductExtraById(string $extraId, string $merchantId): ?ProductExtra;

    // Product-Specific Operations
    public function getProductExtras(string $productId, string $merchantId): Collection;
    public function bulkUpdateProductExtras(array $extrasData, string $productId, string $merchantId): array;

    // Business Logic Operations
    public function validateExtraConstraints(string $extraId, string $merchantId): array;
    public function calculateExtraPriceImpact(array $selectedExtras): float;
    public function checkExtraAvailability(string $extraId): bool;
}
```

---

## 9. ProductBundlesManagementService

### Overview

The ProductBundlesManagementService manages product bundles that group multiple products together with special pricing. It handles bundle creation, validation, and pricing calculations.

### Interface Definition

```php
interface IProductBundlesManagementService {
    // Core CRUD Operations
    public function createProductBundle(array $bundleData): ProductBundle;
    public function updateProductBundle(string $bundleId, array $bundleData): ProductBundle;
    public function deleteProductBundle(string $bundleId): bool;
    public function getProductBundleById(string $bundleId): ?ProductBundle;

    // Bundle Management Operations
    public function getProductBundles(array $filters = []): Collection;
    public function addProductsToBundle(string $bundleId, array $productIds): bool;
    public function removeProductsFromBundle(string $bundleId, array $productIds): bool;

    // Business Logic Operations
    public function calculateBundlePrice(string $bundleId): float;
    public function validateBundleConstraints(string $bundleId): array;
    public function getBundleSavings(string $bundleId): float;
}
```

---

## 10. UnitConversionManagementService

### Overview

The UnitConversionManagementService manages unit conversions for products, enabling merchants to sell products in different units (kg to grams, liters to ml, etc.) with automatic price calculations.

### Interface Definition

```php
interface IUnitConversionManagementService {
    // Core CRUD Operations
    public function createUnitConversion(array $conversionData): UnitConversion;
    public function updateUnitConversion(string $conversionId, array $conversionData): UnitConversion;
    public function deleteUnitConversion(string $conversionId): bool;
    public function getUnitConversionById(string $conversionId): ?UnitConversion;

    // Conversion Operations
    public function getUnitConversions(array $filters = []): Collection;
    public function convertUnit(float $value, string $fromUnit, string $toUnit): float;
    public function calculateConvertedPrice(float $basePrice, string $conversionId): float;

    // Business Logic Operations
    public function validateConversionChain(string $fromUnit, string $toUnit): bool;
    public function getConversionPath(string $fromUnit, string $toUnit): array;
}
```

---

## 11. ProductServiceDetailsManagementService

### Overview

The ProductServiceDetailsManagementService manages service-specific details for products that are services rather than physical goods. It handles scheduling, duration, capacity, and other service-specific attributes.

### Interface Definition

```php
interface IProductServiceDetailsManagementService {
    // Core CRUD Operations
    public function createServiceDetails(array $detailsData, string $productId, string $merchantId): ProductServiceDetail;
    public function updateServiceDetails(string $detailsId, array $detailsData, string $merchantId): ProductServiceDetail;
    public function deleteServiceDetails(string $detailsId, string $merchantId): bool;
    public function getServiceDetailsById(string $detailsId, string $merchantId): ?ProductServiceDetail;

    // Product-Specific Operations
    public function getProductServiceDetails(string $productId, string $merchantId): ?ProductServiceDetail;
    public function updateServiceAvailability(string $detailsId, array $availabilityData, string $merchantId): bool;

    // Business Logic Operations
    public function validateServiceConstraints(string $detailsId, string $merchantId): array;
    public function checkServiceAvailability(string $detailsId, \DateTime $dateTime, int $partySize = 1): bool;
    public function calculateServiceDuration(string $detailsId): int;
}
```

---

## 12. Use Cases

Use cases represent **business workflows** and **frontend-to-backend data flows**. They orchestrate the complete process of handling user requests, coordinating multiple services while **never duplicating service logic**. Use cases focus on:

-   **Request Processing**: Handling complete user workflows (e.g., "create product with variants")
-   **Data Flow Coordination**: Managing how data moves between frontend input and backend services
-   **Transaction Boundaries**: Defining atomic operations that span multiple services
-   **Business Rule Enforcement**: Applying high-level business rules across domains
-   **Event Triggering**: Dispatching events for side effects and integrations

**Key Principle**: Use cases are the **"what"** (business workflows) while services are the **"how"** (domain logic). Use cases delegate all actual business operations to services.

### Use Case Structure

```php
class CreateProductUseCase
{
    public function __construct(
        private ProductManagementService $productService,
        private CategoryManagementService $categoryService,
        private MerchantProductService $merchantService
    ) {}

    public function execute(array $data, string $merchantId): Product
    {
        // Business logic orchestration
        // Validation, authorization, service calls
    }
}
```

### Category Use Cases

#### `Modules/Product/app/UseCases/Category/`

-   **CreateCategoryUseCase**: Creates new category with validation
-   **UpdateCategoryUseCase**: Updates category with hierarchy validation
-   **DeleteCategoryUseCase**: Soft deletes category with dependency checks
-   **GetCategoryTreeUseCase**: Builds category hierarchy for navigation
-   **MoveCategoryUseCase**: Moves category within hierarchy

### Product Use Cases

#### `Modules/Product/app/UseCases/Product/`

Based on database structure where `price` is stored in products (`price` field) and variants (`price` field):

-   **CreateProductUseCase**: Handles complete product creation workflow from frontend data (name, description, base price, variants with their prices, category, etc.)
-   **UpdateProductUseCase**: Manages product updates including base price and variant price changes from frontend input
-   **DeleteProductUseCase**: Manages product deletion with order validation
-   **DuplicateProductUseCase**: Creates product copy with all pricing data (base price + variant prices)
-   **ToggleProductStatusUseCase**: Changes product availability status
-   **BulkUpdateProductsUseCase**: Mass updates including bulk price changes

**Important**: Pricing data flows from frontend as part of product data. Discounts and taxes are calculated externally (Promotion/Order modules) and applied at display/calculation time, not stored in product tables.

### Pricing Use Cases

#### `Modules/Product/app/UseCases/Pricing/`

-   **UpdateProductPriceUseCase**: Updates base price with history logging
-   **ApplyDiscountUseCase**: Applies percentage discounts with validation
-   **BulkUpdatePricesUseCase**: Mass price updates with consistency checks
-   **CalculatePriceWithTaxUseCase**: Computes final prices with tax
-   **GetPriceHistoryUseCase**: Retrieves price change history

### Merchant Use Cases

#### `Modules/Product/app/UseCases/Merchant/`

-   **GetMerchantProductsUseCase**: Retrieves paginated merchant products
-   **CreateMerchantProductUseCase**: Creates product for specific merchant
-   **BulkCreateProductsUseCase**: Mass product creation with validation
-   **TransferProductOwnershipUseCase**: Transfers products between merchants

### Use Case Dependencies

Each use case declares its service dependencies in the constructor:

```php
public function __construct(
    private ProductManagementService $productService,
    private CategoryManagementService $categoryService,
    private ProductPricingService $pricingService,
    private MerchantProductService $merchantService
) {}
```

### Error Handling

Use cases handle business logic errors and return appropriate responses:

```php
public function execute(array $data): Result
{
    try {
        // Business logic
        return Result::success($product);
    } catch (ValidationException $e) {
        return Result::failure('Validation failed', $e->errors());
    } catch (AuthorizationException $e) {
        return Result::failure('Unauthorized', [], 403);
    }
}
```

### Testing

Use cases are tested with mocked services:

```php
public function test_create_product_success()
{
    $productService = Mockery::mock(ProductManagementService::class);
    $useCase = new CreateProductUseCase($productService);

    // Test with mocked dependencies
}
```

---

## 14. Elasticsearch Integration

### Overview

Product management operations trigger asynchronous updates to the Elasticsearch index in the Catalog module for real-time search capabilities. This ensures that product changes are immediately reflected in user-facing search results.

### Event-Driven Updates

Elasticsearch updates are triggered by the following product management events:

#### Product Creation/Update Events

-   **ProductCreated**: Fired when a new product is created
-   **ProductUpdated**: Fired when product basic information is modified (name, description, category, status)
-   **ProductDeleted**: Fired when product is soft-deleted

#### Variant Management Events

-   **VariantCreated**: Fired when a new product variant is added
-   **VariantUpdated**: Fired when variant details are modified (price, attributes, stock)
-   **VariantDeleted**: Fired when variant is removed

#### Pricing Events

-   **ProductPriceUpdated**: Fired when base product price changes
-   **VariantPriceUpdated**: Fired when variant price changes
-   **DiscountApplied**: Fired when discounts are added/modified
-   **DiscountRemoved**: Fired when discounts expire or are removed

#### Category Events

-   **CategoryCreated**: Fired when new category is created
-   **CategoryUpdated**: Fired when category details change
-   **CategoryDeleted**: Fired when category is removed

### Asynchronous Processing with Queues

All Elasticsearch updates are processed asynchronously using Laravel's queue system to ensure:

#### Queue Configuration

-   **Queue Connection**: Redis/database queue for reliability
-   **Queue Name**: `elasticsearch-updates`
-   **Retry Policy**: 3 retry attempts with exponential backoff
-   **Timeout**: 30 seconds per job

#### Queue Job Structure

```php
class UpdateElasticsearchIndex implements ShouldQueue
{
    public function handle()
    {
        // Process document update in Elasticsearch
        // Handle partial failures gracefully
        // Log success/failure for monitoring
    }
}
```

#### Event-to-Queue Flow

1. **Event Fired**: Product management operation completes successfully
2. **Event Listener**: Captures event and dispatches queue job
3. **Queue Processing**: Background worker processes Elasticsearch update
4. **Error Handling**: Failed jobs are retried or logged for manual intervention

### Update Scenarios and Rules

#### Immediate Updates (High Priority)

-   Product status changes (active/inactive)
-   Price changes affecting availability
-   Critical information updates

#### Batch Updates (Normal Priority)

-   Bulk product operations
-   Category hierarchy changes
-   Non-critical metadata updates

#### Deferred Updates (Low Priority)

-   Analytics data updates
-   Historical price tracking
-   Non-search affecting changes

### Data Synchronization Rules

#### Document Structure

Elasticsearch documents include:

-   Product basic information (name, description, category)
-   Pricing data (base price, discounts, final price)
-   Variant information (attributes, prices)
-   Merchant details (name, location for filtering)
-   Availability status
-   Search metadata (tags, keywords)

#### Synchronization Rules

-   **Full Reindex**: Triggered on major schema changes
-   **Partial Updates**: Only modified fields are updated
-   **Delete Handling**: Soft-deleted products are removed from index
-   **Version Control**: Uses product version numbers to prevent stale updates

### Monitoring and Error Handling

#### Logging

-   All queue jobs log start/completion status
-   Elasticsearch operation results are logged
-   Failed operations trigger alerts

#### Error Recovery

-   Failed updates are retried with backoff
-   Critical failures trigger manual intervention workflows
-   Data consistency checks run periodically

#### Performance Considerations

-   Queue jobs are optimized for minimal database queries
-   Elasticsearch bulk operations for multiple updates
-   Circuit breaker pattern for Elasticsearch unavailability

### Integration with Catalog Module

The Product module communicates with Catalog module through:

-   **Event Broadcasting**: Laravel events are broadcast to Catalog listeners
-   **Queue Jobs**: Dedicated jobs handle cross-module communication
-   **API Calls**: Fallback synchronous updates for critical operations

---

## Implementation Notes

### Service Layer Architecture:

#### Service Organization

-   **Domain Services**: Encapsulate business logic while coordinating with repositories
-   **Validation Logic**: Business rule validation within service methods
-   **Event Dispatching**: Laravel events for cross-cutting concerns and integrations
-   **Audit Trail**: Comprehensive logging of business operations

#### Method Design Patterns

-   **Clear Parameters**: Array-based parameters with documented structure
-   **Standard Returns**: Laravel models, collections, or primitive types
-   **Business Validation**: Pre-operation validation with detailed error reporting
-   **Post-Operation Logic**: Cache invalidation, events, and side effects

#### Cross-cutting Concerns

-   **Security**: Merchant isolation, permission validation, audit logging
-   **Performance**: Strategic caching, query optimization, background processing
-   **Monitoring**: Business metrics, error tracking, performance analytics
-   **Compliance**: Audit trails, data retention, regulatory reporting

### Common Patterns:

#### Repository-Service Coordination

-   **Service Delegates to Repository**: Services handle business logic, repositories handle data access
-   **Business Validation in Services**: Validation rules enforced at service layer
-   **Repository Abstraction**: Services work with repository interfaces for testability

#### Business Rule Implementation

-   **Inline Validation**: Business rules implemented directly in service methods
-   **Helper Methods**: Complex validation extracted to private methods
-   **Configuration-Driven Rules**: Business rules configurable per merchant/environment
-   **Rule Categories**: Blocking vs warning validations with appropriate handling

#### Laravel Integration Patterns

-   **Eloquent Models**: Direct use of Laravel models with relationships
-   **Query Builder**: Leveraging Laravel's query capabilities for complex filtering
-   **Caching**: Laravel Cache facade with business-aware keys and TTL
-   **Events**: Laravel event system for decoupling and async processing

### Security Considerations:

#### Multi-Layer Security

-   **Authentication**: JWT-based authentication with merchant context
-   **Authorization**: Role-based and resource-based access control
-   **Data Isolation**: Strict merchant data segregation at all layers
-   **Input Sanitization**: Comprehensive input validation and sanitization
-   **Audit Logging**: Complete audit trail for compliance and forensics

#### Business Security Rules

-   **Ownership Validation**: Every operation validates merchant ownership
-   **Permission Checks**: Granular permissions for different operations
-   **Rate Limiting**: Business-rule based rate limiting per merchant tier
-   **Data Leakage Prevention**: Query result filtering and masking

### Performance Optimizations:

#### Caching Strategy

-   **Multi-Level Caching**: Repository, Service, and Application-level caching
-   **Business-Aware Invalidation**: Cache invalidation based on business events
-   **Query Result Caching**: Complex query results cached with business context
-   **Cache Warming**: Proactive cache population for frequently accessed data

#### Database Optimization

-   **Query Optimization**: N+1 prevention, efficient joins, and indexing
-   **Connection Pooling**: Optimized database connection management
-   **Read Replicas**: Separate read and write database connections
-   **Query Batching**: Bulk operations for multiple related queries

#### Asynchronous Processing

-   **Queue-Based Processing**: Heavy operations processed asynchronously
-   **Event-Driven Architecture**: Decoupled processing for scalability
-   **Background Jobs**: Analytics, reporting, and maintenance tasks
-   **Circuit Breakers**: Fault tolerance for external service dependencies

This specification provides a comprehensive blueprint for implementing a production-ready product management system with proper separation of concerns, security, and performance considerations.
