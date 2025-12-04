<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Setting\Models\Setting;

class MerchantPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policySettings = [
            [
                'key' => 'merchant.profile.ownership',
                'value' => json_encode([
                    'strict_ownership' => true,
                ]),
                'type' => 'object',
                'group' => 'policy.merchant',
                'is_active' => true,
            ],
            [
                'key' => 'merchant.merchant.management',
                'value' => json_encode([
                    'validate_business_category' => true,
                    'require_address_coordinates' => true,
                    'one_address_per_merchant' => true,
                    'validate_coordinates' => true,
                    'require_complete_address' => true,
                ]),
                'type' => 'object',
                'group' => 'policy.merchant',
                'is_active' => true,
            ],
            [
                'key' => 'merchant.schedule.management',
                'value' => json_encode([
                    'support_holidays' => true,
                    'support_temporary_closures' => true,
                    'default_opening_hours' => [
                        'monday' => ['open' => '08:00', 'close' => '17:00', 'is_open' => true],
                        'tuesday' => ['open' => '08:00', 'close' => '17:00', 'is_open' => true],
                        'wednesday' => ['open' => '08:00', 'close' => '17:00', 'is_open' => true],
                        'thursday' => ['open' => '08:00', 'close' => '17:00', 'is_open' => true],
                        'friday' => ['open' => '08:00', 'close' => '17:00', 'is_open' => true],
                        'saturday' => ['open' => '08:00', 'close' => '17:00', 'is_open' => true],
                        'sunday' => ['open' => '08:00', 'close' => '17:00', 'is_open' => false],
                    ],
                ]),
                'type' => 'object',
                'group' => 'policy.merchant',
                'is_active' => true,
            ],
            [
                'key' => 'merchant.settings.management',
                'value' => json_encode([
                    'default_delivery_enabled' => true,
                    'default_delivery_radius_km' => 5,
                    'default_minimum_order_amount' => 0,
                    'default_auto_accept_orders' => true,
                    'default_preparation_time_minutes' => 15,
                    'default_notifications_enabled' => true,
                ]),
                'type' => 'object',
                'group' => 'policy.merchant',
                'is_active' => true,
            ],
        ];

        foreach ($policySettings as $settingData) {
            $settingData['id'] = (string) Str::uuid();

            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                $settingData
            );
        }
    }
}
