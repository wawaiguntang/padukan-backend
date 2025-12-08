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
        'invalid' => 'The provided token is invalid.',
        'missing' => 'Authentication token is required.',
    ],

    'access' => [
        'denied' => 'You do not have permission to access this resource.',
    ],

    'merchant_id_required' => 'Merchant ID is required to access this resource.',
    'merchant_not_found' => 'The specified merchant was not found.',
    'merchant_not_approved' => 'The merchant is not approved to perform this action.',
    'merchant_access_denied' => 'You do not have access to this merchant.',
];
