<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Module Validation Messages
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the product module validation.
    | You are free to modify these language lines according to your
    | application's requirements.
    |
    */

    'name_required' => 'Product name is required.',
    'name_max' => 'Product name cannot exceed :max characters.',
    'description_max' => 'Product description cannot exceed :max characters.',
    'price_required' => 'Product price is required.',
    'price_numeric' => 'Product price must be a valid number.',
    'price_min' => 'Product price must be at least :min.',
    'sku_unique' => 'Product SKU must be unique.',
    'sku_max' => 'Product SKU cannot exceed :max characters.',
    'barcode_unique' => 'Product barcode must be unique.',
    'barcode_max' => 'Product barcode cannot exceed :max characters.',
    'category_exists' => 'Selected category does not exist.',
    'type_required' => 'Product type is required.',
    'type_in' => 'Product type must be one of: :values.',
    'has_variant_boolean' => 'Has variant field must be true or false.',

    // Variant validation
    'variant_name_required' => 'Variant name is required.',
    'variant_price_required' => 'Variant price is required.',
    'variant_price_numeric' => 'Variant price must be a valid number.',
    'variant_price_min' => 'Variant price must be at least :min.',
    'variant_sku_unique' => 'Variant SKU must be unique.',
    'variant_barcode_unique' => 'Variant barcode must be unique.',

    // Category validation
    'category_name_required' => 'Category name is required.',
    'category_slug_unique' => 'Category slug must be unique.',

    // Bulk operations
    'product_ids_required' => 'Product IDs are required.',
    'product_ids_array' => 'Product IDs must be an array.',
    'update_data_required' => 'Update data is required.',

    // Pricing
    'discount_percent_numeric' => 'Discount percent must be a number.',
    'discount_percent_min' => 'Discount percent must be at least :min.',
    'discount_percent_max' => 'Discount percent cannot exceed :max.',
];
