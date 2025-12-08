# Catalog Module Service Specifications

## Overview

This document contains detailed specifications for all services in the Catalog module, focused on user-facing product discovery and search capabilities. The module integrates with Elasticsearch for high-performance search and provides location-based filtering for merchant proximity. User access to products is handled here, while product management operations are in the separate Product module.

## Table of Contents

1. [CatalogSearchService](#1-catalogsearchservice)
2. [CatalogBrowseService](#2-catalogbrowseservice)
3. [CatalogFilterService](#3-catalogfilterservice)
4. [CatalogRecommendationService](#4-catalogrecommendationservice)
5. [Elasticsearch Integration](#5-elasticsearch-integration) - Search index management and synchronization

---

## 1. CatalogSearchService

### Interface Definition

```php
interface ICatalogSearchService {
    public function searchProducts(string $query, array $filters = [], array $location = null, int $perPage = 20): LengthAwarePaginator;
    public function searchByCategory(string $categoryId, array $filters = [], array $location = null, int $perPage = 20): LengthAwarePaginator;
    public function searchByMerchant(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function advancedSearch(array $criteria, array $location = null, int $perPage = 20): LengthAwarePaginator;
    public function getSearchSuggestions(string $query, int $limit = 10): Collection;
    public function getPopularSearchTerms(int $limit = 20): Collection;
    public function getAutocompleteSuggestions(string $query, int $limit = 5): Collection;
    public function getSearchAnalytics(\DateTime $startDate, \DateTime $endDate): array;
}
```

### Method Specifications

#### `searchProducts(string $query, array $filters = [], array $location = null, int $perPage = 20): LengthAwarePaginator`

**Purpose**: Perform full-text search across products with location-based filtering

**Parameters**:

-   `$query` (string): Search query string
-   `$filters` (array): Additional filters
    -   `category_ids` (array, optional): Category UUIDs
    -   `merchant_ids` (array, optional): Merchant UUIDs
    -   `merchant_status` (string, optional): 'open', 'closed', or 'all' (default: 'open')
    -   `price_min` (float, optional): Minimum price
    -   `price_max` (float, optional): Maximum price
    -   `product_types` (array, optional): Product types
    -   `attributes` (array, optional): Attribute filters
    -   `in_stock` (bool, optional): Stock availability
    -   `has_variants` (bool, optional): Variant availability
    -   `rating_min` (float, optional): Minimum rating
-   `$location` (array, optional): User location for proximity filtering
    -   `lat` (float): Latitude
    -   `lng` (float): Longitude
    -   `radius` (int, optional): Search radius in kilometers (default: 10)
-   `$perPage` (int): Results per page (default: 20)

**Returns**: `LengthAwarePaginator` - Paginated search results with distance information

**Search Features**:

-   Full-text search across product names, descriptions, categories
-   Fuzzy matching for typos
-   Relevance scoring with location boost
-   Real-time availability checking

#### `searchByCategory(string $categoryId, array $filters = [], array $location = null, int $perPage = 20): LengthAwarePaginator`

**Purpose**: Search products within specific category with location awareness

**Parameters**: Same as searchProducts, plus categoryId

**Business Rules**:

-   Includes subcategory products
-   Location-based ranking within category
-   Category-specific facet generation

#### `searchByMerchant(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator`

**Purpose**: Search products for specific merchant

**Parameters**: Similar to searchProducts but merchant-specific

#### `advancedSearch(array $criteria, array $location = null, int $perPage = 20): LengthAwarePaginator`

**Purpose**: Multi-criteria advanced search with complex filtering

#### `getSearchSuggestions(string $query, int $limit = 10): Collection`

**Purpose**: Get search term suggestions

**Returns**: `Collection<string>` - Suggested search terms

#### `getPopularSearchTerms(int $limit = 20): Collection`

**Purpose**: Get trending search terms

#### `getAutocompleteSuggestions(string $query, int $limit = 5): Collection`

**Purpose**: Get real-time autocomplete suggestions

#### `getSearchAnalytics(\DateTime $startDate, \DateTime $endDate): array`

**Purpose**: Get search performance analytics

---

## 2. CatalogBrowseService

### Interface Definition

```php
interface ICatalogBrowseService {
    public function getFeaturedProducts(array $location = null, int $limit = 20): Collection;
    public function getProductsByCategory(string $categoryId, array $location = null, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getNearbyMerchants(array $location, int $radius = 10, int $limit = 50, string $status = 'open'): Collection;
    public function getMerchantProducts(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getProductDetails(string $productId, array $location = null): ?array;
    public function getRelatedProducts(string $productId, int $limit = 10): Collection;
    public function getCategoryTree(): Collection;
    public function getMerchantDetails(string $merchantId): ?array;
}
```

### Method Specifications

#### `getFeaturedProducts(array $location = null, int $limit = 20): Collection`

**Purpose**: Get featured/highlighted products for homepage

**Business Rules**:

-   Based on merchant promotions, ratings, popularity
-   Location-aware ordering
-   Cached for performance

#### `getProductsByCategory(string $categoryId, array $location = null, array $filters = [], int $perPage = 20): LengthAwarePaginator`

**Purpose**: Browse products by category with location sorting

#### `getNearbyMerchants(array $location, int $radius = 10, int $limit = 50, string $status = 'open'): Collection`

**Purpose**: Find merchants within specified radius

**Parameters**:

-   `$location`: ['lat' => float, 'lng' => float]
-   `$radius`: Search radius in kilometers
-   `$limit`: Maximum results
-   `$status`: Merchant status filter ('open', 'closed', 'all')

**Returns**: Merchants sorted by distance, filtered by status

#### `getMerchantProducts(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator`

**Purpose**: Get all products from a specific merchant

#### `getProductDetails(string $productId, array $location = null): ?array`

**Purpose**: Get comprehensive product information for detail view

**Returns**: Product data including merchant distance if location provided

#### `getRelatedProducts(string $productId, int $limit = 10): Collection`

**Purpose**: Get related/similar products

**Business Rules**:

-   Based on category, attributes, merchant
-   Uses collaborative filtering algorithms

#### `getCategoryTree(): Collection`

**Purpose**: Get hierarchical category structure for navigation

#### `getMerchantDetails(string $merchantId): ?array`

**Purpose**: Get merchant information for catalog display

---

## 3. CatalogFilterService

### Interface Definition

```php
interface ICatalogFilterService {
    public function getAvailableFilters(array $currentFilters = []): array;
    public function getPriceRange(?string $categoryId = null, array $location = null): array;
    public function getCategoryFacets(array $filters = [], array $location = null): Collection;
    public function getAttributeFacets(string $attributeKey, array $filters = [], array $location = null): Collection;
    public function getMerchantFacets(array $filters = [], array $location = null): Collection;
    public function getLocationFacets(array $filters = []): array;
    public function applyFilters(array $baseQuery, array $filters, array $location = null): array;
    public function validateFilters(array $filters): array;
}
```

### Method Specifications

#### `getAvailableFilters(array $currentFilters = []): array`

**Purpose**: Get all available filter options based on current context

**Returns**:

```php
[
    'categories' => [...],
    'price_ranges' => ['min' => 1000, 'max' => 100000],
    'attributes' => [...],
    'merchants' => [...],
    'locations' => [...]
]
```

#### `getPriceRange(?string $categoryId = null, array $location = null): array`

**Purpose**: Get dynamic price range for filtering

#### `getCategoryFacets(array $filters = [], array $location = null): Collection`

**Purpose**: Get category facets with product counts

#### `getAttributeFacets(string $attributeKey, array $filters = [], array $location = null): Collection`

**Purpose**: Get attribute values with counts

#### `getMerchantFacets(array $filters = [], array $location = null): Collection`

**Purpose**: Get merchant facets with product counts

#### `getLocationFacets(array $filters = []): array`

**Purpose**: Get location-based filter options

#### `applyFilters(array $baseQuery, array $filters, array $location = null): array`

**Purpose**: Apply complex filter logic to Elasticsearch queries

#### `validateFilters(array $filters): array`

**Purpose**: Validate filter parameters

---

## 4. CatalogRecommendationService

### Interface Definition

```php
interface ICatalogRecommendationService {
    public function getPersonalizedRecommendations(string $userId, array $location = null, int $limit = 10): Collection;
    public function getTrendingProducts(array $location = null, int $limit = 20): Collection;
    public function getSimilarProducts(string $productId, int $limit = 10): Collection;
    public function getMerchantRecommendations(string $merchantId, int $limit = 10): Collection;
    public function getCategoryRecommendations(string $categoryId, array $location = null, int $limit = 10): Collection;
    public function trackUserBehavior(string $userId, string $action, string $productId): void;
    public function getRecommendationAnalytics(\DateTime $startDate, \DateTime $endDate): array;
}
```

### Method Specifications

#### `getPersonalizedRecommendations(string $userId, array $location = null, int $limit = 10): Collection`

**Purpose**: Get AI-powered personalized product recommendations

**Business Rules**:

-   Based on user search history, purchases, browsing behavior
-   Location-aware recommendations
-   Machine learning algorithms for relevance

#### `getTrendingProducts(array $location = null, int $limit = 20): Collection`

**Purpose**: Get currently trending products

#### `getSimilarProducts(string $productId, int $limit = 10): Collection`

**Purpose**: Find products similar to given product

#### `getMerchantRecommendations(string $merchantId, int $limit = 10): Collection`

**Purpose**: Recommend products from same merchant

#### `getCategoryRecommendations(string $categoryId, array $location = null, int $limit = 10): Collection`

**Purpose**: Recommend products in same category

#### `trackUserBehavior(string $userId, string $action, string $productId): void`

**Purpose**: Track user interactions for recommendation engine

**Actions**: 'view', 'search', 'add_to_cart', 'purchase'

#### `getRecommendationAnalytics(\DateTime $startDate, \DateTime $endDate): array`

**Purpose**: Get recommendation performance analytics

---

## 5. Elasticsearch Integration

### Overview

The Catalog module uses Elasticsearch as the primary search engine for high-performance product discovery. All product data is indexed and kept synchronized with the Product module through event-driven updates.

### Index Management

#### Index Structure

-   **Primary Index**: `products_catalog`
-   **Alias**: `products` for zero-downtime reindexing
-   **Mapping**: Comprehensive product schema with nested objects

#### Document Structure

```json
{
  "product_id": "uuid",
  "name": "Product Name",
  "description": "Product description",
  "category": {
    "id": "uuid",
    "name": "Category Name",
    "path": ["Parent", "Child"]
  },
  "merchant": {
    "id": "uuid",
    "name": "Merchant Name",
    "status": "open", // "open" or "closed" - from Merchant module
    "location": {
      "lat": 123.456,
      "lng": 789.012
    },
    "operating_hours": {
      "monday": {"open": "08:00", "close": "22:00"},
      "tuesday": {"open": "08:00", "close": "22:00"}
      // ... other days
    }
  },
  "pricing": {
    "base_price": 50000,
    "current_price": 45000, // calculated with discounts
    "discount_percent": 10, // from Product/Pricing module
    "discount_amount": 5000,
    "currency": "IDR"
  },
  "variants": [...],
  "attributes": [...],
  "availability": {
    "in_stock": true, // from Inventory module
    "stock_quantity": 100, // from Inventory module
    "low_stock_threshold": 20
  },
  "rating": 4.5,
  "tags": ["tag1", "tag2"],
  "search_keywords": ["keyword1", "keyword2"]
}
```

**Data Source Notes**:

-   **Product Basic Info**: From Product module (name, description, category, attributes)
-   **Merchant Details**: From Merchant module (name, status, location, operating hours)
-   **Pricing & Discounts**: From Product/Pricing module (base price, discounts, final price)
-   **Stock/Inventory**: From Inventory module (stock levels, availability)
-   **Ratings & Reviews**: From Review/Rating module (aggregated ratings)
-   **Search Keywords**: Auto-generated from product data

### Synchronization with Product Module

#### Event Listeners

The Catalog module listens to multiple module events and updates Elasticsearch accordingly:

**Product Module Events**:

-   **ProductCreated**: Index new product document
-   **ProductUpdated**: Update existing document
-   **ProductDeleted**: Remove from index
-   **VariantCreated/Updated/Deleted**: Update product variants
-   **PriceUpdated**: Update pricing information
-   **CategoryChanged**: Update category information

**Merchant Module Events**:

-   **MerchantStatusChanged**: Update merchant status (open/closed)
-   **MerchantLocationUpdated**: Update merchant location and operating hours
-   **MerchantProfileUpdated**: Update merchant name and basic info

**Inventory Module Events** (when enabled):

-   **StockUpdated**: Update product availability and stock levels
-   **LowStockAlert**: Update low stock indicators

**Review/Rating Module Events**:

-   **RatingUpdated**: Update product rating aggregations

#### Queue Processing

-   **Queue**: `catalog-elasticsearch-updates`
-   **Retry Logic**: 3 attempts with exponential backoff
-   **Bulk Operations**: Group multiple updates for efficiency

### Search Query Building

#### Basic Search

-   Multi-field search across name, description, category, merchant
-   Fuzzy matching for typos
-   Relevance scoring with custom boost factors

#### Location-Based Search

-   Geo-distance queries for proximity filtering
-   Distance sorting and boosting
-   Radius-based filtering

#### Advanced Filtering

-   Range queries for price
-   Term queries for categories, attributes
-   Bool queries for complex combinations

### Performance Optimizations

#### Caching Strategy

-   Query result caching with TTL
-   Filter facet caching
-   Merchant location data caching

#### Index Optimization

-   Regular index maintenance and optimization
-   Shard configuration for scalability
-   Replica setup for high availability

### Analytics and Monitoring

#### Search Analytics

-   Query performance metrics
-   Popular search terms tracking
-   Conversion rate analysis
-   No-result query analysis

#### Index Health Monitoring

-   Index size and growth tracking
-   Query performance monitoring
-   Error rate tracking
-   Synchronization lag monitoring

---

## Implementation Notes

### Service Layer Architecture

-   **Dependency Injection**: Services use constructor injection
-   **Interface Segregation**: Each service has its own interface
-   **Elasticsearch Client**: Centralized ES client with connection pooling
-   **Caching**: Multi-level caching (Redis + in-memory)
-   **Error Handling**: Comprehensive error handling with fallbacks

### Security Considerations

-   **Data Isolation**: User data isolation (no sensitive merchant data)
-   **Rate Limiting**: API rate limiting for search endpoints
-   **Input Validation**: Strict validation of search parameters
-   **Audit Logging**: All search operations logged for analytics

### Performance Considerations

-   **Pagination**: Efficient deep pagination with search_after
-   **Async Processing**: Background processing for heavy operations
-   **Circuit Breaker**: Protection against Elasticsearch failures
-   **Load Balancing**: Distributed search across multiple nodes

This specification provides a comprehensive blueprint for implementing a high-performance, location-aware product catalog with Elasticsearch integration.
