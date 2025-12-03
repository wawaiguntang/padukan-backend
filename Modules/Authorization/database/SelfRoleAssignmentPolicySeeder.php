<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SelfRoleAssignmentPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policySettings = [
            [
                'id' => Str::uuid(),
                'key' => 'self_role_assignment',
                'name' => 'Self Role Assignment Policy',
                'settings' => json_encode([
                    'valid_role_types' => ['customer', 'driver', 'merchant'],
                    'allow_multiple_roles' => true,
                    'prevent_duplicates' => true,
                    'self_assignment_only' => true
                ]),
                'is_active' => true,
                'description' => 'Policy settings for self role assignment functionality'
            ]
        ];

        DB::table('policy_settings')->insert($policySettings);
    }
}