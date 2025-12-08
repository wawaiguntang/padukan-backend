<?php

return [
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
