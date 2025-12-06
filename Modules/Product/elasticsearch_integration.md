# Elasticsearch Integration & Data Synchronization

## Overview

The Product module integrates with Elasticsearch for advanced search capabilities. All product data is automatically synchronized to Elasticsearch indices through event-driven architecture.

## 19. ProductSearchIndexService

**Purpose**: Elasticsearch index management and real-time synchronization

### Core Methods:

-   `indexProduct(string $productId)` - Index single product to Elasticsearch
-   `bulkIndexProducts(array $productIds)` - Bulk index multiple products
-   `removeFromIndex(string $productId)` - Remove product from search index
-   `updateProductIndex(string $productId)` - Update existing product in index
-   `reindexAllProducts(string $merchantId = null)` - Full reindex operation
-   `searchProducts(array $query, array $filters, int $from = 0, int $size = 20)` - Elasticsearch search
-   `getProductSuggestions(string $query, int $limit = 10)` - Autocomplete suggestions
-   `getSearchFacets(array $filters)` - Generate search facets
-   `optimizeIndex()` - Index optimization and maintenance

### Event-Driven Synchronization:

-   **`ProductCreated` Event** → `IndexProductListener` → Add to Elasticsearch
-   **`ProductUpdated` Event** → `UpdateProductIndexListener` → Update in Elasticsearch
-   **`ProductDeleted` Event** → `RemoveProductFromIndexListener` → Remove from Elasticsearch
-   **`ProductVariantCreated` Event** → `UpdateProductVariantsListener` → Update product variants
-   **`ProductVariantUpdated` Event** → `UpdateProductVariantsListener` → Update product variants
-   **`ProductVariantDeleted` Event** → `UpdateProductVariantsListener` → Update product variants
-   **`CategoryUpdated` Event** → `UpdateProductCategoriesListener` → Update category paths
-   **`InventoryUpdated` Event** → `UpdateProductStockListener` → Update stock status

## Elasticsearch Index Structure

### Product Index Mapping:

```json
{
    "mappings": {
        "properties": {
            "id": { "type": "keyword" },
            "merchant_id": { "type": "keyword" },
            "name": { "type": "text", "analyzer": "standard" },
            "slug": { "type": "keyword" },
            "description": { "type": "text", "analyzer": "standard" },
            "type": { "type": "keyword" },
            "category_id": { "type": "keyword" },
            "category_path": { "type": "keyword" },
            "category_names": { "type": "text" },
            "price": { "type": "float" },
            "has_variant": { "type": "boolean" },
            "has_expired": { "type": "boolean" },
            "is_active": { "type": "boolean" },
            "tags": { "type": "keyword" },
            "attributes": {
                "type": "nested",
                "properties": {
                    "key": { "type": "keyword" },
                    "value": { "type": "keyword" },
                    "name": { "type": "text" }
                }
            },
            "variants": {
                "type": "nested",
                "properties": {
                    "id": { "type": "keyword" },
                    "name": { "type": "text" },
                    "sku": { "type": "keyword" },
                    "price": { "type": "float" },
                    "stock_quantity": { "type": "integer" },
                    "attributes": { "type": "nested" }
                }
            },
            "created_at": { "type": "date" },
            "updated_at": { "type": "date" },
            "merchant_name": { "type": "text" },
            "average_rating": { "type": "float" },
            "total_reviews": { "type": "integer" },
            "total_sales": { "type": "integer" }
        }
    }
}
```

## Synchronization Strategies

### Real-time Sync (Event-Driven):

-   **Immediate consistency** for critical updates (price, stock, status)
-   **Event listeners** automatically trigger index updates
-   **Queue-based processing** for high-throughput operations
-   **Error handling** with retry mechanisms and dead letter queues

### Batch Sync (Scheduled):

-   **Full reindex** operations for schema changes
-   **Incremental updates** for bulk data changes
-   **Index optimization** and maintenance tasks
-   **Data consistency checks** between database and Elasticsearch

### Hybrid Approach:

-   **Real-time** for user-facing changes (creates, updates, deletes)
-   **Batch** for background analytics updates (ratings, sales counts)
-   **On-demand** for administrative operations (reindex, cleanup)

## Event Listeners Implementation

### ProductEventService

**Purpose**: Central event dispatching for product-related operations

#### Methods:

-   `dispatchProductCreated(Product $product)` - Dispatch product creation event
-   `dispatchProductUpdated(Product $product, array $changes)` - Dispatch product update event
-   `dispatchProductDeleted(string $productId, string $merchantId)` - Dispatch product deletion event
-   `dispatchBulkOperation(string $operation, array $productIds, array $data)` - Dispatch bulk operation event
-   `dispatchSearchIndexUpdate(string $productId, string $operation)` - Dispatch search index update

### SearchIndexEventListeners

**Purpose**: Handle Elasticsearch synchronization events

#### Methods:

-   `handleProductCreated(ProductCreated $event)` - Index new product
-   `handleProductUpdated(ProductUpdated $event)` - Update product in index
-   `handleProductDeleted(ProductDeleted $event)` - Remove product from index
-   `handleBulkProductsIndexed(BulkProductsIndexed $event)` - Handle bulk indexing
-   `handleSearchIndexMaintenance(SearchIndexMaintenance $event)` - Handle maintenance operations

## Performance Optimizations

### Indexing Strategies:

-   **Partial updates** instead of full reindexing
-   **Bulk operations** for multiple product updates
-   **Index aliases** for zero-downtime reindexing
-   **Shard optimization** based on data volume

### Caching Integration:

-   **Redis caching** for frequently searched products
-   **Cache invalidation** on index updates
-   **Fallback to database** when Elasticsearch is unavailable
-   **Circuit breaker pattern** for Elasticsearch failures

### Monitoring & Observability:

-   **Index health monitoring** with automated alerts
-   **Query performance tracking** and optimization
-   **Synchronization lag monitoring** between database and index
-   **Error rate tracking** for failed indexing operations

## Data Consistency Guarantees

### Eventual Consistency:

-   **Conflict resolution** strategies for concurrent updates
-   **Version-based optimistic locking** for data integrity
-   **Idempotent operations** to handle duplicate events
-   **Compensation actions** for failed operations

### Data Validation:

-   **Schema validation** before indexing
-   **Data transformation** pipelines
-   **Sanitization** of search data
-   **Duplicate detection** and handling

This Elasticsearch integration ensures **high-performance search capabilities** with **real-time data synchronization**, providing users with fast, accurate, and up-to-date search results across the entire product catalog.
