<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Middleware Language Lines for Product Module
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by middleware classes in the
    | Product module for various validation and access control messages.
    |
    */

    'token' => [
        'invalid' => 'Token yang diberikan tidak valid.',
        'missing' => 'Token autentikasi diperlukan.',
    ],

    'access' => [
        'denied' => 'Anda tidak memiliki izin untuk mengakses sumber daya ini.',
    ],

    'merchant_id_required' => 'ID pedagang diperlukan untuk mengakses sumber daya ini.',
    'merchant_not_found' => 'Pedagang yang ditentukan tidak ditemukan.',
    'merchant_not_approved' => 'Pedagang tidak disetujui untuk melakukan tindakan ini.',
    'merchant_access_denied' => 'Anda tidak memiliki akses ke pedagang ini.',
];
