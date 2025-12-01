<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver Status Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for driver status related
    | messages in the driver module.
    |
    */

    // Status Messages
    'retrieved_successfully' => 'Driver status retrieved successfully',
    'online_status_updated' => 'Online status updated successfully',
    'operational_status_updated' => 'Operational status updated successfully',
    'active_service_updated' => 'Active service updated successfully',
    'location_updated' => 'Location updated successfully',
    'update_failed' => 'Failed to update status',

    // Permission Messages
    'cannot_update_online_status' => 'You cannot update your online status',
    'cannot_update_operational_status' => 'You cannot update your operational status',
    'operational_status_system_controlled' => 'Operational status is controlled by the system',
    'cannot_set_active_service' => 'You cannot set this active service',
    'service_not_available_for_vehicles' => 'This service is not available with your verified vehicles',
    'vehicle_not_found_or_not_verified' => 'Vehicle not found or not verified',
    'vehicle_id' => [
        'required' => 'Vehicle ID is required when going online',
        'uuid' => 'Vehicle ID must be a valid UUID',
        'exists' => 'The selected vehicle does not exist',
    ],
    'cannot_update_active_service' => 'You cannot update your active service',
    'cannot_update_location' => 'You cannot update your location',

    // Attributes
    'online_status' => 'Online Status',
    'operational_status' => 'Operational Status',
    'active_service' => 'Active Service',
    'vehicle_id' => 'Vehicle ID',
    'latitude' => 'Latitude',
    'longitude' => 'Longitude',

    // Validation Messages
    'validation' => [
        'online_status' => [
            'required' => 'Online status is required',
            'in' => 'Online status must be either online or offline',
        ],
        'operational_status' => [
            'required' => 'Operational status is required',
            'in' => 'Operational status must be available, on_order, or rest',
        ],
        'active_service' => [
            'required' => 'Active service is required',
            'in' => 'Active service must be one of: food, ride, car, send, mart',
        ],
        'latitude' => [
            'required' => 'Latitude is required',
            'numeric' => 'Latitude must be a number',
            'between' => 'Latitude must be between -90 and 90 degrees',
        ],
        'longitude' => [
            'required' => 'Longitude is required',
            'numeric' => 'Longitude must be a number',
            'between' => 'Longitude must be between -180 and 180 degrees',
        ],
    ],
];
