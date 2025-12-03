<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\PolicySetting;

class DriverPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Driver module policy settings...');

        $policies = [
            [
                'key' => 'driver.profile.ownership',
                'name' => 'Driver Profile Ownership Policy',
                'settings' => [
                    'strict_ownership' => true,
                    'check_user_active' => true,
                ],
                'description' => 'Ensures drivers can only access their own profile data',
            ],
            [
                'key' => 'driver.profile.verification',
                'name' => 'Driver Profile Verification Policy',
                'settings' => [
                    'require_id_card' => true,
                    'id_card_meta_required' => ['name', 'number', 'address'],
                    'allow_resubmit_only_rejected' => true,
                    'verification_grace_period' => 30,
                    'require_manual_review' => true,
                ],
                'description' => 'Controls profile verification with ID card requirements',
            ],
            [
                'key' => 'driver.vehicle.ownership',
                'name' => 'Driver Vehicle Ownership Policy',
                'settings' => [
                    'strict_ownership' => true,
                    'check_user_active' => true,
                ],
                'description' => 'Ensures drivers can only access their own vehicles',
            ],
            [
                'key' => 'driver.vehicle.management',
                'name' => 'Driver Vehicle Management Policy',
                'settings' => [
                    'max_vehicles_per_driver' => 3,
                    'max_motorcycle_per_driver' => 1,
                    'max_car_per_driver' => 1,
                    'require_verification' => true,
                    'allowed_vehicle_types' => ['motorcycle', 'car'],
                    'require_sim' => true,
                    'require_stnk' => true,
                    'require_vehicle_photo' => true,
                ],
                'description' => 'Manages driver vehicle creation (max 3 vehicles total, 1 car + 1 motorcycle max)',
            ],
            [
                'key' => 'driver.vehicle.verification',
                'name' => 'Driver Vehicle Verification Policy',
                'settings' => [
                    'required_documents' => ['sim', 'stnk', 'vehicle_photo'],
                    'sim_meta_required' => ['number', 'expiry_date'],
                    'stnk_meta_required' => ['expiry_date'],
                    'vehicle_photo_meta_required' => ['front_view', 'back_view', 'left_view', 'right_view'],
                    'allow_resubmit_only_rejected' => true,
                    'verification_grace_period' => 30,
                    'require_manual_review' => true,
                ],
                'description' => 'Controls vehicle verification with required documents',
            ],
            [
                'key' => 'driver.vehicle.update',
                'name' => 'Driver Vehicle Update Policy',
                'settings' => [
                    'allow_update_only_rejected' => true,
                    'allowed_update_statuses' => ['rejected'],
                    'require_verification_after_update' => true,
                    'reset_verification_on_update' => true,
                ],
                'description' => 'Controls vehicle update operations - only allowed when rejected',
            ],
            [
                'key' => 'driver.document.verification_upload',
                'name' => 'Driver Verification Document Upload Policy',
                'settings' => [
                    'max_file_size' => 10485760, // 10MB
                    'allowed_mime_types' => [
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                        'image/gif',
                        'image/webp',
                        'application/pdf',
                    ],
                    'require_verification' => true,
                    'auto_process' => false,
                    'storage_path' => 'documents',
                    'storage_disk' => 's3',
                    'storage_visibility' => 'private'
                ],
                'description' => 'Controls verification document upload validation and storage',
            ],
            [
                'key' => 'driver.profile.avatar',
                'name' => 'Driver Profile Avatar Policy',
                'settings' => [
                    'max_file_size' => 5242880, // 5MB
                    'allowed_mime_types' => [
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                        'image/gif',
                        'image/webp'
                    ],
                    'max_dimensions' => [
                        'width' => 4096,
                        'height' => 4096,
                    ],
                    'storage_path' => 'avatars',
                    'storage_disk' => 's3',
                    'storage_visibility' => 'public'
                ],
                'description' => 'Controls driver profile avatar upload and management',
            ],
            [
                'key' => 'driver.status.management',
                'name' => 'Driver Status & Service Management Policy',
                'settings' => [
                    // Status Management
                    'driver_controlled' => [
                        'online_status' => true,        // Driver can set online/offline
                        'operational_status' => false,  // System controls operational status
                        'active_service' => true,       // Driver can choose service
                        'location' => true,             // Driver can update location
                    ],
                    'system_controlled' => [
                        'operational_status' => true,   // System manages available/on_order/rest
                        'auto_offline_after_inactivity' => 30, // minutes
                        'auto_operational_status_change' => true,
                    ],
                    'require_location_for_online' => true,
                    'validate_service_availability' => true,
                    'max_location_update_frequency' => 60, // seconds
                    'status_change_log_retention' => 90, // days

                    // Service Validation
                    'require_verified_vehicle' => true,
                    'validate_vehicle_service_mapping' => true,
                    'motorcycle_services' => ['ride', 'food', 'send', 'mart'],
                    'car_services' => ['car', 'send'],
                    'allow_service_switching' => true,
                    'service_switch_cooldown' => 300, // seconds
                    'max_concurrent_services' => 1,

                    // Location Tracking
                    'require_gps_accuracy' => 100, // meters
                    'max_location_age' => 300, // seconds
                    'location_update_interval' => 30, // seconds
                    'privacy_mode_enabled' => false,
                    'location_history_retention' => 24, // hours (for current location only)
                    'geofence_enabled' => true,
                    'allowed_countries' => ['ID'], // Indonesia only

                    // Rate Limiting
                    'max_orders_per_hour' => 10,
                    'max_orders_per_day' => 50,
                    'order_acceptance_timeout' => 30, // seconds
                    'auto_pause_after_rejections' => 3,
                    'pause_duration' => 900, // seconds (15 minutes)
                    'rejection_rate_threshold' => 0.3, // 30%
                ],
                'description' => 'Comprehensive policy for driver status, service validation, location tracking, and rate limiting',
            ],
        ];

        foreach ($policies as $policyData) {
            PolicySetting::firstOrCreate(
                ['key' => $policyData['key']],
                [
                    'name' => $policyData['name'],
                    'settings' => $policyData['settings'],
                    'is_active' => true,
                    'description' => $policyData['description'],
                ]
            );
        }

        $this->command->info('Created ' . count($policies) . ' driver policy settings');
    }
}
