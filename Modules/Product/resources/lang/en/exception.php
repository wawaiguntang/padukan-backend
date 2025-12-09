<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Module Exception Messages
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the product module exceptions.
    | You are free to modify these language lines according to your
    | application's requirements.
    |
    */

    'product_not_found' => 'Product with ID :id not found.',
    'product_not_found_for_merchant' => 'Product with ID :id not found for merchant :merchant_id.',
    'product_access_denied' => 'Access denied to product :id for merchant :merchant_id.',
    'variant_not_found' => 'Product variant with ID :id not found.',
    'validation_failed' => 'Product validation failed.',
    'product_limit_exceeded' => 'Product limit exceeded for merchant :merchant_id. Current: :current, Maximum: :max.',
    'category_not_found' => 'Category with ID :id not found.',
    'category_access_denied' => 'Access denied to category :id.',
    'duplicate_sku' => 'SKU :sku already exists.',
    'duplicate_barcode' => 'Barcode :barcode already exists.',
    'invalid_price' => 'Invalid price value.',
    'invalid_discount' => 'Invalid discount percentage.',
    'product_has_variants' => 'Cannot delete product with active variants.',
    'variant_has_orders' => 'Cannot delete variant with active orders.',
    'merchant_not_found' => 'Merchant with ID :id not found.',
    'transaction_failed' => 'Transaction failed during :operation operation. Error: :error',

    /*
    |--------------------------------------------------------------------------
    | Category Exception Messages
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by category-related exceptions.
    |
    */

    'category' => [
        'not_found' => 'Category not found.',
        'already_exists' => 'Category already exists.',
        'validation_failed' => 'Category validation failed.',
        'hierarchy_violation' => 'Category hierarchy constraint violated.',
        'self_reference' => 'Category cannot be its own parent.',
        'hierarchy_depth_exceeded' => 'Maximum category hierarchy depth exceeded.',
        'has_children' => 'Cannot delete category that has child categories.',
        'has_products' => 'Cannot delete category that has associated products.',
        'deletion_constraints_not_met' => 'Category deletion constraints not met.',
    ],
];
