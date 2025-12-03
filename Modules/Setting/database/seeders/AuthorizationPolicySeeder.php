<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Models\Setting;
use Illuminate\Support\Str;

class AuthorizationPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policySettings = [
            [
                'key' => 'self_role_assignment',
                'value' => json_encode([
                    'valid_role_types' => ['customer', 'driver', 'merchant'],
                    'allow_multiple_roles' => true
                ]),
                'type' => 'object',
                'group' => 'policy.authorization',
                'is_active' => true,
            ]
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
