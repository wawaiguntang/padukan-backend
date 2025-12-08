<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Models\Setting;

class SettingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AuthorizationPolicySeeder::class,
            CustomerPolicySeeder::class,
            DriverPolicySeeder::class,
            MerchantPolicySeeder::class,
        ]);

        $this->seedCoreSettings();
    }

    /**
     * Seed core application settings
     */
    private function seedCoreSettings(): void
    {
        $coreSettings = [
            'app' => [
                'name' => 'Padukan',
                'description' => 'Padukan Super App Platform',
                'version' => '1.0.0',
                'maintenance_mode' => false,
                'maintenance_message' => 'The application is under maintenance. Please check back later.',
            ],
            'pricing' => [
                'product_markup' => [
                    'food' => [
                        'type' => 'percentage', // 'percentage' or 'nominal'
                        'value' => 15.0, // 15% markup for food
                        'enabled' => true,
                    ],
                    'mart' => [
                        'type' => 'percentage', // 'percentage' or 'nominal'
                        'value' => 12.0, // 12% markup for mart items
                        'enabled' => true,
                    ],
                    'service' => [
                        'type' => 'percentage', // 'percentage' or 'nominal'
                        'value' => 20.0, // 20% markup for services
                        'enabled' => true,
                    ],
                ],
                'application_cost' => [
                    'type' => 'nominal', // 'percentage' or 'nominal'
                    'value' => 0, // 10% of order value or Rp 2,000 fixed fee
                    'enabled' => true,
                ],
                'ride' => [
                    'base_fare' => 4000, // Rp 4,000 base fare
                    'per_km' => 1500, // Rp 1,500 per km
                    'per_minute' => 200, // Rp 200 per minute
                    'minimum_distance' => 1.5, // Minimum 1.5km charge
                    'maximum_distance' => 100.0, // Maximum 100km
                    'booking_fee' => 1500, // Rp 1,500 booking fee
                ],
                'car' => [
                    'base_fare' => 8000, // Rp 8,000 base fare
                    'per_km' => 3500, // Rp 3,500 per km
                    'per_minute' => 400, // Rp 400 per minute
                    'minimum_distance' => 2.0, // Minimum 2km charge
                    'maximum_distance' => 200.0, // Maximum 200km
                    'booking_fee' => 2500, // Rp 2,500 booking fee
                ],
                'surge_pricing' => [
                    'rain_multiplier' => 1.5, // 50% increase during rain
                    'crowd_multiplier' => 2.0, // 100% increase during crowd
                    'peak_hours_multiplier' => 1.3, // 30% increase during peak hours
                    'enabled' => true,
                ],
                'time_based' => [
                    'night_surcharge' => [
                        'enabled' => true,
                        'start_time' => '22:00',
                        'end_time' => '05:00',
                        'multiplier' => 1.2, // 20% increase at night
                    ],
                    'weekend_surcharge' => [
                        'enabled' => true,
                        'multiplier' => 1.1, // 10% increase on weekends
                    ]
                ]
            ]
        ];

        Setting::updateOrCreate(
            ['key' => 'core.setting'],
            [
                'key' => 'core.setting',
                'value' => $coreSettings,
                'type' => 'array',
                'group' => 'core',
                'is_active' => true,
            ]
        );
    }
}
