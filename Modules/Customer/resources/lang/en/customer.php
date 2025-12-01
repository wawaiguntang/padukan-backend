<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Customer Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in the customer module for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'profile' => [
        'not_found' => 'We couldn\'t find that profile.',
        'already_exists' => 'A profile already exists for this user.',
        'created' => 'Profile created successfully.',
        'updated' => 'Profile updated successfully.',
        'deleted' => 'Profile deleted successfully.',
        'avatar' => [
            'uploaded' => 'Avatar uploaded successfully.',
            'deleted' => 'Avatar deleted successfully.',
        ],
    ],

    'document' => [
        'not_found' => 'We couldn\'t find that document.',
        'uploaded' => 'Document uploaded successfully.',
        'deleted' => 'Document deleted successfully.',
        'verification' => [
            'status_updated' => 'Document verification status updated.',
        ],
    ],

    'address' => [
        'not_found' => 'We couldn\'t find that address.',
        'created' => 'Address created successfully.',
        'updated' => 'Address updated successfully.',
        'deleted' => 'Address deleted successfully.',
        'set_primary' => 'Address set as primary successfully.',
    ],

    'file' => [
        'upload_failed' => 'File upload failed.',
        'validation_failed' => 'File validation failed.',
        'delete_failed' => 'File deletion failed.',
        'invalid_type' => 'Invalid file type.',
        'too_large' => 'File is too large.',
        'avatar' => [
            'invalid_dimensions' => 'Avatar image dimensions are too large.',
            'not_image' => 'Uploaded file is not a valid image.',
        ],
        'document' => [
            'invalid_type' => 'Document file type not allowed.',
        ],
    ],

    'validation' => [
        'failed' => 'Validation failed.',
    ],
];
