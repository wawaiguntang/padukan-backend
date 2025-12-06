# Product Module Database Schema Specifications

## Overview

This document provides detailed specifications for the Product module database schema. Each table includes field descriptions, data types, constraints, relationships, and business logic explanations.

## Table of Contents

1. [categories](#1-categories) - Product category hierarchy
2. [attribute_master](#2-attribute_master) - Global product attributes
3. [attribute_custom](#3-attribute_custom) - Merchant-specific attributes
4. [products](#4-products) - Main product catalog
5. [product_variants](#5-product_variants) - Product variations
6. [product_extras](#6-product_extras) - Additional product options
7. [product_bundles](#7-product_bundles) - Product bundle configurations
8. [unit_conversions](#8-unit_conversions) - Unit conversion ratios
9. [product_service_details](#9-product_service_details) - Service-specific information

---

## 1. categories

**Purpose**: Hierarchical product categorization system supporting nested categories with path tracking.

### Schema

```sql
CREATE TABLE categories (
    id UUID PRIMARY KEY,
    parent_id UUID NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    path VARCHAR(255) NULL,
    level INTEGER NULL,
    metadata JSONB NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Indexes
CREATE INDEX idx_categories_parent_id ON categories(parent_id);
CREATE INDEX idx_categories_slug ON categories(slug);
```

### Field Specifications

#### `id` (UUID, Primary Key)

-   **Type**: UUID
-   **Purpose**: Unique identifier for each category
-   **Generation**: Auto-generated using UUID v4
-   **Usage**: Referenced by products and used in URLs

#### `parent_id` (UUID, Nullable, Foreign Key)

-   **Type**: UUID
-   **Purpose**: References parent category for hierarchical structure
-   **Constraints**: Must reference existing category or be NULL for root categories
-   **Usage**: Enables nested category trees (e.g., Electronics > Smartphones > Android)

#### `name` (VARCHAR(255), Required)

-   **Type**: VARCHAR(255)
-   **Purpose**: Human-readable category name
-   **Constraints**: Required, max 255 characters
-   **Usage**: Display name in UI, navigation menus, breadcrumbs

#### `slug` (VARCHAR(255), Required, Unique)

-   **Type**: VARCHAR(255)
-   **Purpose**: URL-friendly identifier for SEO and routing
-   **Constraints**: Required, unique across all categories, max 255 characters
-   **Format**: Lowercase, hyphens instead of spaces (e.g., "smart-phones")
-   **Usage**: SEO-friendly URLs, API endpoints

#### `description` (TEXT, Nullable)

-   **Type**: TEXT
-   **Purpose**: Detailed category description for SEO and user guidance
-   **Constraints**: Optional, unlimited length
-   **Usage**: Category pages, meta descriptions, help text

#### `path` (VARCHAR(255), Nullable)

-   **Type**: VARCHAR(255)
-   **Purpose**: Full hierarchical path from root to current category
-   **Format**: Slash-separated slugs (e.g., "electronics/smartphones/android")
-   **Usage**: Breadcrumb generation, hierarchical queries, URL structure

#### `level` (INTEGER, Nullable)

-   **Type**: INTEGER
-   **Purpose**: Depth level in category hierarchy (0 = root, 1 = child, etc.)
-   **Constraints**: Auto-calculated based on parent relationship
-   **Usage**: Limiting hierarchy depth, styling different levels

#### `metadata` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Flexible storage for category-specific data
-   **Structure**:
    ```json
    {
        "icon": "fas fa-mobile",
        "color": "#007bff",
        "featured": true,
        "commission_rate": 0.05,
        "seo_keywords": ["electronics", "gadgets"]
    }
    ```
-   **Usage**: UI customization, business rules, SEO optimization

#### `created_at` (TIMESTAMP, Required)

-   **Type**: TIMESTAMP
-   **Purpose**: Record creation timestamp
-   **Default**: Current timestamp

#### `updated_at` (TIMESTAMP, Required)

-   **Type**: TIMESTAMP
-   **Purpose**: Last modification timestamp
-   **Auto-update**: Updated on every change

### Relationships

-   **Self-referencing**: parent_id → categories.id (hierarchical)
-   **One-to-Many**: categories.id → products.category_id

### Business Rules

-   **Hierarchy Depth**: Maximum 5 levels to prevent complexity
-   **Unique Slugs**: No duplicate slugs across all categories
-   **Path Updates**: Automatic path recalculation when hierarchy changes
-   **Cascade Operations**: Careful handling of category moves/deletions

---

## 2. attribute_master

**Purpose**: Global product attributes available to all merchants (e.g., Size, Color, Material).

### Schema

```sql
CREATE TABLE attribute_master (
    id UUID PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    key VARCHAR(255) NOT NULL UNIQUE,
    metadata JSONB NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Indexes
CREATE INDEX idx_attribute_master_key ON attribute_master(key);
```

### Field Specifications

#### `id` (UUID, Primary Key)

-   **Type**: UUID
-   **Purpose**: Unique identifier for master attribute
-   **Usage**: Referenced by product variants for attribute combinations

#### `name` (VARCHAR(255), Required)

-   **Type**: VARCHAR(255)
-   **Purpose**: Human-readable attribute name
-   **Examples**: "Size", "Color", "Material", "Brand"
-   **Usage**: Display in UI, filter labels

#### `key` (VARCHAR(255), Required, Unique)

-   **Type**: VARCHAR(255)
-   **Purpose**: Machine-readable identifier for API and internal use
-   **Constraints**: Required, unique, lowercase with underscores
-   **Format**: snake_case (e.g., "size", "color", "material_type")
-   **Usage**: API parameters, database queries, configuration

#### `metadata` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Attribute configuration and validation rules
-   **Structure**:
    ```json
    {
        "type": "select|text|number",
        "options": ["S", "M", "L", "XL"],
        "validation": {
            "required": true,
            "min_length": 1,
            "max_length": 50
        },
        "display_order": 1,
        "filterable": true
    }
    ```
-   **Usage**: UI rendering, validation rules, search configuration

#### `created_at` & `updated_at` (TIMESTAMP)

-   Standard timestamp fields for audit trail

### Relationships

-   **Referenced by**: product_variants.attribute_master_ids (JSON array)

### Business Rules

-   **Global Scope**: Available to all merchants
-   **Immutable Keys**: Keys cannot be changed once created
-   **System Attributes**: Pre-seeded with common attributes
-   **Validation**: Strict format validation for keys

---

## 3. attribute_custom

**Purpose**: Merchant-specific custom attributes for specialized product properties.

### Schema

```sql
CREATE TABLE attribute_custom (
    id UUID PRIMARY KEY,
    merchant_id UUID NOT NULL,
    name VARCHAR(255) NOT NULL,
    key VARCHAR(255) NOT NULL,
    metadata JSONB NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    UNIQUE(merchant_id, key)
);

-- Indexes
CREATE INDEX idx_attribute_custom_merchant_id ON attribute_custom(merchant_id);
```

### Field Specifications

#### `id` (UUID, Primary Key)

-   **Type**: UUID
-   **Purpose**: Unique identifier for custom attribute

#### `merchant_id` (UUID, Required)

-   **Type**: UUID
-   **Purpose**: Owner merchant identifier
-   **Usage**: Scopes attributes to specific merchants

#### `name` (VARCHAR(255), Required)

-   **Type**: VARCHAR(255)
-   **Purpose**: Human-readable attribute name
-   **Examples**: "Kadar Gula", "Tingkat Kepedasan", "Alergen"

#### `key` (VARCHAR(255), Required)

-   **Type**: VARCHAR(255)
-   **Purpose**: Machine-readable identifier
-   **Constraints**: Unique per merchant
-   **Format**: snake_case, merchant-scoped

#### `metadata` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Attribute configuration specific to merchant needs
-   **Structure**: Similar to attribute_master but merchant-specific

#### `created_at` & `updated_at` (TIMESTAMP)

-   Standard timestamp fields

### Relationships

-   **Foreign Key**: merchant_id → merchants.id (assumed external)
-   **Referenced by**: product_variants.attribute_custom_ids (JSON array)

### Business Rules

-   **Merchant Isolation**: Attributes scoped to specific merchants
-   **Unique Keys**: Keys must be unique within merchant scope
-   **Flexibility**: Merchants can define custom attributes for their products

---

## 4. products

**Purpose**: Main product catalog containing all product information and configurations.

### Schema

```sql
CREATE TABLE products (
    id UUID PRIMARY KEY,
    merchant_id UUID NOT NULL,
    category_id UUID NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    type ENUM('food', 'mart', 'service') NOT NULL,
    barcode VARCHAR(255) NULL,
    sku VARCHAR(255) NULL,
    base_unit VARCHAR(255) NULL,
    price DECIMAL(15,2) NULL,
    has_variant BOOLEAN NOT NULL DEFAULT FALSE,
    has_expired BOOLEAN NOT NULL DEFAULT FALSE,
    metadata JSONB NULL,
    version INTEGER NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    deleted_at TIMESTAMP NULL
);

-- Indexes
CREATE UNIQUE INDEX idx_products_merchant_sku ON products(merchant_id, sku);
CREATE UNIQUE INDEX idx_products_merchant_barcode ON products(merchant_id, barcode);
CREATE INDEX idx_products_merchant_id ON products(merchant_id);
CREATE INDEX idx_products_category_id ON products(category_id);
CREATE INDEX idx_products_slug ON products(slug);
CREATE INDEX idx_products_type ON products(type);
```

### Field Specifications

#### `id` (UUID, Primary Key)

-   **Type**: UUID
-   **Purpose**: Unique product identifier across the system

#### `merchant_id` (UUID, Required)

-   **Type**: UUID
-   **Purpose**: Product owner identifier
-   **Usage**: Multi-tenant data isolation

#### `category_id` (UUID, Nullable)

-   **Type**: UUID
-   **Purpose**: Product category classification
-   **Foreign Key**: References categories.id

#### `name` (VARCHAR(255), Required)

-   **Type**: VARCHAR(255)
-   **Purpose**: Product display name
-   **Usage**: UI display, search indexing

#### `slug` (VARCHAR(255), Required, Unique)

-   **Type**: VARCHAR(255)
-   **Purpose**: SEO-friendly URL identifier
-   **Constraints**: Globally unique across all products

#### `description` (TEXT, Nullable)

-   **Type**: TEXT
-   **Purpose**: Detailed product description
-   **Usage**: Product pages, search indexing

#### `type` (ENUM, Required)

-   **Type**: ENUM('food', 'mart', 'service')
-   **Purpose**: Product type classification
-   **Values**:
    -   `food`: Food and beverage products
    -   `mart`: General merchandise
    -   `service`: Service-based offerings

#### `barcode` (VARCHAR(255), Nullable)

-   **Type**: VARCHAR(255)
-   **Purpose**: Product barcode for inventory/pos systems
-   **Constraints**: Unique per merchant

#### `sku` (VARCHAR(255), Nullable)

-   **Type**: VARCHAR(255)
-   **Purpose**: Stock keeping unit identifier
-   **Constraints**: Unique per merchant

#### `base_unit` (VARCHAR(255), Nullable)

-   **Type**: VARCHAR(255)
-   **Purpose**: Base unit of measurement (pcs, kg, liter, etc.)
-   **Usage**: Inventory calculations, pricing

#### `price` (DECIMAL(15,2), Nullable)

-   **Type**: DECIMAL(15,2)
-   **Purpose**: Base product price
-   **Constraints**: >= 0 when provided
-   **Note**: NULL when product has variants (price defined per variant)

#### `has_variant` (BOOLEAN, Required, Default: FALSE)

-   **Type**: BOOLEAN
-   **Purpose**: Indicates if product has multiple variants
-   **Usage**: UI logic, pricing calculations

#### `has_expired` (BOOLEAN, Required, Default: FALSE)

-   **Type**: BOOLEAN
-   **Purpose**: Product availability status
-   **Usage**: Filtering active products

#### `metadata` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Flexible product-specific data
-   **Structure**:
    ```json
    {
        "brand": "Samsung",
        "weight_grams": 150,
        "dimensions": { "length": 10, "width": 5, "height": 2 },
        "tags": ["electronics", "smartphone"],
        "seo_keywords": ["galaxy", "android"],
        "images": ["url1.jpg", "url2.jpg"]
    }
    ```

#### `version` (INTEGER, Required, Default: 1)

-   **Type**: INTEGER
-   **Purpose**: Optimistic locking for concurrent updates
-   **Usage**: Prevents lost updates during editing

#### `deleted_at` (TIMESTAMP, Nullable)

-   **Type**: TIMESTAMP
-   **Purpose**: Soft delete timestamp
-   **Usage**: Preserves data integrity while hiding deleted products

### Relationships

-   **Foreign Key**: merchant_id → merchants.id
-   **Foreign Key**: category_id → categories.id
-   **One-to-Many**: products.id → product_variants.product_id
-   **One-to-Many**: products.id → product_extras.product_id
-   **One-to-Many**: products.id → product_service_details.product_id

### Business Rules

-   **Merchant Isolation**: Products scoped to specific merchants
-   **Unique Identifiers**: SKU and barcode unique per merchant
-   **Variant Logic**: If has_variant=true, price should be NULL
-   **Soft Deletes**: Maintains referential integrity

---

## 5. product_variants

**Purpose**: Product variations with specific attributes, pricing, and inventory.

### Schema

```sql
CREATE TABLE product_variants (
    id UUID PRIMARY KEY,
    product_id UUID NOT NULL,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(255) NULL,
    barcode VARCHAR(255) NULL,
    attribute_master_ids JSONB NULL,
    attribute_custom_ids JSONB NULL,
    unit VARCHAR(255) NULL,
    conversion_id UUID NULL,
    price DECIMAL(15,2) NOT NULL,
    has_expired BOOLEAN NOT NULL DEFAULT FALSE,
    metadata JSONB NULL,
    version INTEGER NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Indexes
CREATE UNIQUE INDEX idx_product_variants_sku ON product_variants(sku);
CREATE UNIQUE INDEX idx_product_variants_barcode ON product_variants(barcode);
CREATE INDEX idx_product_variants_product_id ON product_variants(product_id);
CREATE INDEX idx_product_variants_conversion_id ON product_variants(conversion_id);
CREATE INDEX idx_product_variants_has_expired ON product_variants(has_expired);
```

### Field Specifications

#### `id` (UUID, Primary Key)

-   **Type**: UUID
-   **Purpose**: Unique variant identifier

#### `product_id` (UUID, Required, Foreign Key)

-   **Type**: UUID
-   **Purpose**: Parent product reference
-   **Constraints**: Cascade delete

#### `name` (VARCHAR(255), Required)

-   **Type**: VARCHAR(255)
-   **Purpose**: Variant display name (e.g., "Large Red", "256GB")
-   **Usage**: UI display, order details

#### `sku` (VARCHAR(255), Nullable, Unique)

-   **Type**: VARCHAR(255)
-   **Purpose**: Variant-specific SKU
-   **Constraints**: Globally unique across all variants

#### `barcode` (VARCHAR(255), Nullable, Unique)

-   **Type**: VARCHAR(255)
-   **Purpose**: Variant-specific barcode
-   **Constraints**: Globally unique across all variants

#### `attribute_master_ids` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Array of master attribute IDs defining this variant
-   **Format**: ["attr-uuid-1", "attr-uuid-2"]
-   **Usage**: Variant identification and filtering

#### `attribute_custom_ids` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Array of custom attribute IDs for this variant
-   **Format**: ["custom-attr-uuid-1", "custom-attr-uuid-2"]

#### `unit` (VARCHAR(255), Nullable)

-   **Type**: VARCHAR(255)
-   **Purpose**: Unit of measurement for this variant
-   **Usage**: Inventory and pricing calculations

#### `conversion_id` (UUID, Nullable, Foreign Key)

-   **Type**: UUID
-   **Purpose**: Reference to unit conversion rule
-   **Foreign Key**: References unit_conversions.id

#### `price` (DECIMAL(15,2), Required)

-   **Type**: DECIMAL(15,2)
-   **Purpose**: Variant-specific price
-   **Constraints**: Required, > 0

#### `has_expired` (BOOLEAN, Required, Default: FALSE)

-   **Type**: BOOLEAN
-   **Purpose**: Variant availability status

#### `metadata` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Variant-specific additional data
-   **Structure**:
    ```json
    {
        "weight_grams": 500,
        "dimensions": { "l": 30, "w": 20, "h": 5 },
        "special_handling": true
    }
    ```

#### `version` (INTEGER, Required, Default: 1)

-   **Type**: INTEGER
-   **Purpose**: Optimistic locking for variant updates

#### `deleted_at` (TIMESTAMP, Nullable)

-   **Type**: TIMESTAMP
-   **Purpose**: Soft delete for variants

### Relationships

-   **Foreign Key**: product_id → products.id (cascade delete)
-   **Foreign Key**: conversion_id → unit_conversions.id
-   **Referenced by**: product_service_details.variant_id

### Business Rules

-   **Unique Combinations**: Attribute combinations must be unique per product
-   **Required Price**: Every variant must have a price
-   **Global Identifiers**: SKU and barcode globally unique
-   **Cascade Deletion**: Variants deleted when parent product is deleted

---

## 6. product_extras

**Purpose**: Additional options or add-ons that can be selected with products (e.g., extra cheese, gift wrapping).

### Schema

```sql
CREATE TABLE product_extras (
    id UUID PRIMARY KEY,
    product_id UUID NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    required BOOLEAN NOT NULL DEFAULT FALSE,
    max_qty INTEGER NULL,
    metadata JSONB NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Indexes
CREATE INDEX idx_product_extras_product_id ON product_extras(product_id);
```

### Field Specifications

#### `id` (UUID, Primary Key)

-   **Type**: UUID
-   **Purpose**: Unique extra option identifier

#### `product_id` (UUID, Required, Foreign Key)

-   **Type**: UUID
-   **Purpose**: Parent product reference
-   **Constraints**: Cascade delete

#### `name` (VARCHAR(255), Required)

-   **Type**: VARCHAR(255)
-   **Purpose**: Extra option name (e.g., "Extra Cheese", "Gift Wrapping")
-   **Usage**: Display in UI, order details

#### `price` (DECIMAL(15,2), Required)

-   **Type**: DECIMAL(15,2)
-   **Purpose**: Additional cost for this extra
-   **Constraints**: >= 0

#### `required` (BOOLEAN, Required, Default: FALSE)

-   **Type**: BOOLEAN
-   **Purpose**: Whether this extra is mandatory
-   **Usage**: UI validation, ordering logic

#### `max_qty` (INTEGER, Nullable)

-   **Type**: INTEGER
-   **Purpose**: Maximum quantity that can be selected
-   **Constraints**: > 0 when provided
-   **Usage**: UI limits, validation

#### `metadata` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Extra-specific configuration
-   **Structure**:
    ```json
    {
        "category": "toppings",
        "calories": 150,
        "vegetarian": true,
        "available_days": ["monday", "wednesday", "friday"]
    }
    ```

### Relationships

-   **Foreign Key**: product_id → products.id (cascade delete)

### Business Rules

-   **Price Validation**: Extra prices must be non-negative
-   **Required Logic**: If required=true, max_qty should be >= 1
-   **Product Scope**: Extras are product-specific

---

## 7. product_bundles

**Purpose**: Pre-configured product bundles combining multiple products or services.

### Schema

```sql
CREATE TABLE product_bundles (
    id UUID PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    metadata JSONB NULL,
    version INTEGER NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    deleted_at TIMESTAMP NULL
);

-- Indexes
CREATE INDEX idx_product_bundles_name ON product_bundles(name);
```

### Field Specifications

#### `id` (UUID, Primary Key)

-   **Type**: UUID
-   **Purpose**: Unique bundle identifier

#### `name` (VARCHAR(255), Required, Unique)

-   **Type**: VARCHAR(255)
-   **Purpose**: Bundle display name
-   **Constraints**: Globally unique

#### `metadata` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Bundle configuration and items
-   **Structure**:
    ```json
    {
        "description": "Complete lunch package",
        "items": [
            {
                "product_id": "uuid",
                "product_type": "food",
                "qty": 1,
                "mandatory": true
            }
        ],
        "total_price": 75000,
        "discount_percent": 10,
        "valid_from": "2025-01-01",
        "valid_to": "2025-12-31"
    }
    ```

#### `version` (INTEGER, Required, Default: 1)

-   **Type**: INTEGER
-   **Purpose**: Bundle version for change tracking

#### `deleted_at` (TIMESTAMP, Nullable)

-   **Type**: TIMESTAMP
-   **Purpose**: Soft delete for bundles

### Business Rules

-   **Unique Names**: Bundle names must be globally unique
-   **Version Control**: Track changes to bundle composition
-   **Flexible Items**: Support different product types in bundles

---

## 8. unit_conversions

**Purpose**: Unit conversion ratios for inventory and pricing calculations.

### Schema

```sql
CREATE TABLE unit_conversions (
    id UUID PRIMARY KEY,
    from_unit VARCHAR(255) NOT NULL,
    to_unit VARCHAR(255) NOT NULL,
    multiply_factor DECIMAL(15,6) NOT NULL,
    metadata JSONB NULL,
    parent_conversion_id UUID NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (parent_conversion_id) REFERENCES unit_conversions(id) ON DELETE SET NULL
);

-- Indexes
CREATE INDEX idx_unit_conversions_from_unit ON unit_conversions(from_unit);
CREATE INDEX idx_unit_conversions_to_unit ON unit_conversions(to_unit);
CREATE INDEX idx_unit_conversions_parent_conversion_id ON unit_conversions(parent_conversion_id);
```

### Field Specifications

#### `id` (UUID, Primary Key)

-   **Type**: UUID
-   **Purpose**: Unique conversion rule identifier

#### `from_unit` (VARCHAR(255), Required)

-   **Type**: VARCHAR(255)
-   **Purpose**: Source unit (e.g., "box", "pack", "kg")
-   **Usage**: Conversion starting point

#### `to_unit` (VARCHAR(255), Required)

-   **Type**: VARCHAR(255)
-   **Purpose**: Target unit (e.g., "pcs", "liter", "gram")
-   **Usage**: Conversion end point

#### `multiply_factor` (DECIMAL(15,6), Required)

-   **Type**: DECIMAL(15,6)
-   **Purpose**: Conversion multiplier
-   **Examples**:
    -   Box to Pack: 10 (1 box = 10 packs)
    -   Pack to Pieces: 5 (1 pack = 5 pieces)
-   **Constraints**: > 0

#### `metadata` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Conversion-specific data
-   **Structure**:
    ```json
    {
        "description": "Standard retail packaging",
        "industry_standard": true,
        "precision": 2
    }
    ```

#### `parent_conversion_id` (UUID, Nullable, Foreign Key)

-   **Type**: UUID
-   **Purpose**: Parent conversion for multi-level conversions
-   **Usage**: Enables complex conversion chains (box → pack → pcs)

### Relationships

-   **Self-referencing**: parent_conversion_id → unit_conversions.id
-   **Referenced by**: product_variants.conversion_id

### Business Rules

-   **Positive Factors**: Multiply factor must be greater than 0
-   **Valid Chains**: Prevent circular references in conversion chains
-   **Precision**: High precision decimal for accurate calculations

---

## 9. product_service_details

**Purpose**: Additional details specific to service-type products (duration, staffing, capacity).

### Schema

```sql
CREATE TABLE product_service_details (
    service_id UUID PRIMARY KEY,
    product_id UUID NOT NULL,
    variant_id UUID NULL,
    duration_minutes INTEGER NULL,
    staff_required INTEGER NULL,
    min_participants INTEGER NULL,
    max_participants INTEGER NULL,
    optional_extras JSONB NULL,
    metadata JSONB NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
);

-- Indexes
CREATE INDEX idx_product_service_details_product_id ON product_service_details(product_id);
CREATE INDEX idx_product_service_details_variant_id ON product_service_details(variant_id);
```

### Field Specifications

#### `service_id` (UUID, Primary Key)

-   **Type**: UUID
-   **Purpose**: Unique service detail identifier
-   **Note**: Uses service_id instead of id for clarity

#### `product_id` (UUID, Required, Foreign Key)

-   **Type**: UUID
-   **Purpose**: Associated product
-   **Constraints**: Cascade delete

#### `variant_id` (UUID, Nullable, Foreign Key)

-   **Type**: UUID
-   **Purpose**: Associated product variant (if applicable)
-   **Constraints**: Cascade delete

#### `duration_minutes` (INTEGER, Nullable)

-   **Type**: INTEGER
-   **Purpose**: Service duration in minutes
-   **Constraints**: > 0 when provided
-   **Usage**: Scheduling, pricing calculations

#### `staff_required` (INTEGER, Nullable)

-   **Type**: INTEGER
-   **Purpose**: Number of staff members required
-   **Constraints**: > 0 when provided

#### `min_participants` (INTEGER, Nullable)

-   **Type**: INTEGER
-   **Purpose**: Minimum number of participants
-   **Constraints**: > 0 when provided

#### `max_participants` (INTEGER, Nullable)

-   **Type**: INTEGER
-   **Purpose**: Maximum number of participants
-   **Constraints**: >= min_participants when both provided

#### `optional_extras` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Service-specific additional options
-   **Structure**:
    ```json
    [
        {
            "name": "Premium Setup",
            "price": 50000,
            "max_qty": 1
        }
    ]
    ```

#### `metadata` (JSONB, Nullable)

-   **Type**: JSONB
-   **Purpose**: Service-specific configuration
-   **Structure**:
    ```json
    {
        "equipment_needed": ["projector", "sound_system"],
        "special_requirements": "Quiet environment required",
        "cancellation_policy": "24 hours notice"
    }
    ```

### Relationships

-   **Foreign Key**: product_id → products.id (cascade delete)
-   **Foreign Key**: variant_id → product_variants.id (cascade delete)

### Business Rules

-   **Service Products Only**: Only applicable to products with type='service'
-   **Participant Validation**: max_participants >= min_participants
-   **Duration Validation**: Positive duration when specified
-   **Variant-Specific**: Can be defined per variant for different service levels

---

## Database Design Principles

### Normalization

-   **3NF Compliance**: Proper normalization to reduce redundancy
-   **Referential Integrity**: Foreign key constraints maintain data consistency
-   **Atomic Data**: Single-purpose fields with appropriate data types

### Performance Optimization

-   **Strategic Indexing**: Indexes on frequently queried fields
-   **JSONB Usage**: Flexible data storage with query capabilities
-   **UUID Primary Keys**: Globally unique identifiers for distributed systems
-   **Soft Deletes**: Maintain data integrity while supporting logical deletion

### Scalability Considerations

-   **Partitioning Ready**: Schema designed for future partitioning
-   **Horizontal Scaling**: UUID keys support database sharding
-   **Caching Support**: Metadata fields support caching strategies
-   **Audit Trail**: Comprehensive timestamp tracking

### Data Integrity

-   **Constraints**: Database-level constraints prevent invalid data
-   **Cascading Deletes**: Automatic cleanup of related records
-   **Unique Constraints**: Prevent duplicate business data
-   **Check Constraints**: Business rule enforcement at database level

This schema provides a robust foundation for a comprehensive product management system with support for complex product hierarchies, flexible attributes, inventory management, and service-specific configurations.
