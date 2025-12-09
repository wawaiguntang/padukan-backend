<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Module Validation Messages
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan oleh validasi modul produk.
    | Anda bebas memodifikasi baris bahasa ini sesuai dengan
    | kebutuhan aplikasi Anda.
    |
    */

    'name_required' => 'Nama produk wajib diisi.',
    'name_max' => 'Nama produk tidak boleh melebihi :max karakter.',
    'description_max' => 'Deskripsi produk tidak boleh melebihi :max karakter.',
    'price_required' => 'Harga produk wajib diisi.',
    'price_numeric' => 'Harga produk harus berupa angka yang valid.',
    'price_min' => 'Harga produk minimal :min.',
    'sku_unique' => 'SKU produk harus unik.',
    'sku_max' => 'SKU produk tidak boleh melebihi :max karakter.',
    'barcode_unique' => 'Barcode produk harus unik.',
    'barcode_max' => 'Barcode produk tidak boleh melebihi :max karakter.',
    'category_exists' => 'Kategori yang dipilih tidak ada.',
    'type_required' => 'Tipe produk wajib diisi.',
    'type_in' => 'Tipe produk harus salah satu dari: :values.',
    'has_variant_boolean' => 'Field memiliki varian harus true atau false.',

    // Variant validation
    'variant_name_required' => 'Nama varian wajib diisi.',
    'variant_price_required' => 'Harga varian wajib diisi.',
    'variant_price_numeric' => 'Harga varian harus berupa angka yang valid.',
    'variant_price_min' => 'Harga varian minimal :min.',
    'variant_sku_unique' => 'SKU varian harus unik.',
    'variant_barcode_unique' => 'Barcode varian harus unik.',

    // Category validation
    'category_name_required' => 'Nama kategori wajib diisi.',
    'category_slug_unique' => 'Slug kategori harus unik.',

    // Bulk operations
    'product_ids_required' => 'ID produk wajib diisi.',
    'product_ids_array' => 'ID produk harus berupa array.',
    'update_data_required' => 'Data update wajib diisi.',

    // Pricing
    'discount_percent_numeric' => 'Persentase diskon harus berupa angka.',
    'discount_percent_min' => 'Persentase diskon minimal :min.',
    'discount_percent_max' => 'Persentase diskon tidak boleh melebihi :max.',
];
