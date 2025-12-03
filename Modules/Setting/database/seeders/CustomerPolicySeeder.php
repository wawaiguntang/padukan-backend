<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Setting\Models\Setting;

class CustomerPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policySettings = [
            [
                'key' => 'customer.profile.ownership',
                'value' => json_encode([
                    'strict_ownership' => true,
                ]),
                'type' => 'object',
                'group' => 'policy.customer',
                'is_active' => true,
            ],
            [
                'key' => 'customer.address.management',
                'value' => json_encode([
                    'max_addresses_per_customer' => 10,
                    'validate_coordinates' => true,
                ]),
                'type' => 'object',
                'group' => 'policy.customer',
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
