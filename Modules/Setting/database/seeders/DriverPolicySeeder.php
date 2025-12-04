<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Setting\Models\Setting;

class DriverPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policySettings = [
            [
                'key' => 'driver.profile.ownership',
                'group' => 'driver_profile',
                'type' => 'array',
                'value' => [
                    'strict_ownership' => true,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'driver.vehicle.ownership',
                'group' => 'driver_vehicle',
                'type' => 'array',
                'value' => [
                    'strict_ownership' => true,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'driver.vehicle.management',
                'group' => 'driver_vehicle',
                'type' => 'array',
                'value' => [
                    'max_vehicles_per_driver' => 2,
                    'max_motorcycle_per_driver' => 1,
                    'max_car_per_driver' => 1,
                    'require_verification' => true,
                    'allowed_vehicle_types' => ['motorcycle', 'car'],
                    'require_sim' => true,
                    'require_stnk' => true,
                    'require_vehicle_photo' => true,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'driver.vehicle.verification',
                'group' => 'driver_vehicle',
                'type' => 'array',
                'value' => [
                    'required_documents' => ['sim', 'stnk', 'vehicle_photo'],
                    'sim_meta_required' => ['number', 'expiry_date'],
                    'stnk_meta_required' => ['expiry_date'],
                    'vehicle_photo_meta_required' => ['front_view', 'back_view', 'left_view', 'right_view'],
                    'allow_resubmit_only_rejected' => true,
                    'verification_grace_period' => 30,
                    'require_manual_review' => true,
                    'reset_verification_on_update' => true,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'driver.status.management',
                'group' => 'driver_status',
                'type' => 'array',
                'value' => [
                    'require_location_for_online' => true,
                    'validate_service_availability' => true,

                    // Service Validation
                    'require_verified_vehicle' => true,
                    'validate_vehicle_service_mapping' => true,
                    'motorcycle_services' => ['ride', 'food', 'send', 'mart'],
                    'car_services' => ['car', 'send'],
                    'allow_service_switching' => true,
                    'service_switch_cooldown' => 300, // seconds
                    'max_motorcycle_services' => 4,
                    'max_car_services' => 2,
                    'allow_multiple_active_vehicles' => false,

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
                'is_active' => true,
            ],
        ];

        foreach ($policySettings as $settingData) {
            // Use the model's setTypedValue method to properly set the type and value
            $setting = new Setting();
            $setting->key = $settingData['key'];
            $setting->group = $settingData['group'];
            $setting->is_active = $settingData['is_active'];
            $setting->setTypedValue($settingData['value']);

            Setting::updateOrCreate(
                ['key' => $setting->key],
                [
                    'group' => $setting->group,
                    'type' => $setting->type,
                    'value' => $setting->value,
                    'is_active' => $setting->is_active,
                ]
            );
        }
    }
}
