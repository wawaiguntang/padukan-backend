<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Module Exception Messages
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan oleh exception modul produk.
    | Anda bebas memodifikasi baris bahasa ini sesuai dengan
    | kebutuhan aplikasi Anda.
    |
    */

    'product_not_found' => 'Produk dengan ID :id tidak ditemukan.',
    'product_not_found_for_merchant' => 'Produk dengan ID :id tidak ditemukan untuk merchant :merchant_id.',
    'product_access_denied' => 'Akses ditolak untuk produk :id oleh merchant :merchant_id.',
    'variant_not_found' => 'Varian produk dengan ID :id tidak ditemukan.',
    'validation_failed' => 'Validasi produk gagal.',
    'product_limit_exceeded' => 'Batas produk terlampaui untuk merchant :merchant_id. Saat ini: :current, Maksimum: :max.',
    'category_not_found' => 'Kategori dengan ID :id tidak ditemukan.',
    'category_access_denied' => 'Akses ditolak untuk kategori :id.',
    'duplicate_sku' => 'SKU :sku sudah ada.',
    'duplicate_barcode' => 'Barcode :barcode sudah ada.',
    'invalid_price' => 'Nilai harga tidak valid.',
    'invalid_discount' => 'Persentase diskon tidak valid.',
    'product_has_variants' => 'Tidak dapat menghapus produk yang memiliki varian aktif.',
    'variant_has_orders' => 'Tidak dapat menghapus varian yang memiliki pesanan aktif.',
    'merchant_not_found' => 'Merchant dengan ID :id tidak ditemukan.',
    'transaction_failed' => 'Transaksi gagal selama operasi :operation. Error: :error',

    /*
    |--------------------------------------------------------------------------
    | Category Exception Messages
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan oleh exception terkait kategori.
    |
    */

    'category' => [
        'not_found' => 'Kategori tidak ditemukan.',
        'already_exists' => 'Kategori sudah ada.',
        'validation_failed' => 'Validasi kategori gagal.',
        'hierarchy_violation' => 'Pelanggaran batasan hierarki kategori.',
        'self_reference' => 'Kategori tidak dapat menjadi parent dari dirinya sendiri.',
        'hierarchy_depth_exceeded' => 'Kedalaman maksimum hierarki kategori terlampaui.',
        'has_children' => 'Tidak dapat menghapus kategori yang memiliki sub-kategori.',
        'has_products' => 'Tidak dapat menghapus kategori yang memiliki produk terkait.',
        'deletion_constraints_not_met' => 'Batasan penghapusan kategori tidak terpenuhi.',
    ],
];
