<?php

return [
    'first_name' => [
        'string' => 'First name must be a string',
        'max' => 'First name may not be greater than :max characters',
    ],
    'last_name' => [
        'string' => 'Last name must be a string',
        'max' => 'Last name may not be greater than :max characters',
    ],
    'gender' => [
        'in' => 'The selected gender is invalid',
    ],
    'language' => [
        'string' => 'Language must be a string',
        'max' => 'Language may not be greater than :max characters',
    ],
    'avatar_file' => [
        'image' => 'Avatar must be an image',
        'mimes' => 'Avatar must be a file of type: :values',
        'max' => 'Avatar may not be greater than :max kilobytes',
    ],
];
