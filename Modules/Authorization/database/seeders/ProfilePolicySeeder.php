<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\PolicySetting;

class ProfilePolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Profile module policy settings...');

        $policies = [
            [
                'key' => 'profile.ownership',
                'name' => 'Profile Ownership Policy',
                'settings' => [
                    'enabled' => true,
                    'strict_ownership' => true,
                    'check_user_active' => true,
                ],
                'description' => 'Ensures users can only access their own profile data',
            ],
            [
                'key' => 'profile.document_status',
                'name' => 'Document Status Policy',
                'settings' => [
                    'enabled' => true,
                    'allowed_update_statuses' => ['pending', 'rejected'],
                    'allowed_delete_statuses' => ['rejected'],
                    'allowed_resubmit_statuses' => ['rejected'],
                ],
                'description' => 'Controls document operations based on verification status',
            ],
            [
                'key' => 'profile.business_type',
                'name' => 'Business Type Policy',
                'settings' => [
                    'enabled' => true,
                    'allowed_types' => ['food', 'mart'],
                    'require_business_type' => true,
                    'auto_validate' => true,
                    'custom_validation_rules' => [],
                ],
                'description' => 'Validates merchant business types',
            ],
            [
                'key' => 'profile.vehicle_ownership',
                'name' => 'Vehicle Ownership Policy',
                'settings' => [
                    'enabled' => true,
                    'max_vehicles_per_driver' => 2,
                    'require_verification' => true,
                    'allowed_vehicle_types' => ['motorcycle', 'car'],
                    'auto_verify_owned' => false,
                ],
                'description' => 'Manages driver vehicle ownership and limits',
            ],
            [
                'key' => 'profile.bank_account',
                'name' => 'Bank Account Policy',
                'settings' => [
                    'enabled' => true,
                    'max_accounts_per_merchant' => 3,
                    'require_primary_account' => true,
                    'allowed_banks' => [], // Empty means all banks allowed
                    'verification_required' => true,
                    'auto_verify_primary' => false,
                ],
                'description' => 'Controls merchant bank account management',
            ],
            [
                'key' => 'profile.address_management',
                'name' => 'Address Management Policy',
                'settings' => [
                    'enabled' => true,
                    'max_addresses_per_profile' => 5,
                    'require_primary_address' => true,
                    'allowed_address_types' => ['home', 'work', 'business', 'other'],
                    'require_coordinates' => true,
                    'coordinate_validation' => [
                        'latitude_range' => [-90, 90],
                        'longitude_range' => [-180, 180],
                    ],
                ],
                'description' => 'Manages profile address creation and validation',
            ],
            [
                'key' => 'profile.document_upload',
                'name' => 'Document Upload Policy',
                'settings' => [
                    'enabled' => true,
                    'max_file_size' => 5242880, // 5MB
                    'allowed_mime_types' => [
                        'image/jpeg', 'image/png', 'image/jpg',
                        'application/pdf'
                    ],
                    'require_verification' => true,
                    'auto_process' => false,
                    'storage_disk' => 'documents',
                    'retention_days' => 365,
                ],
                'description' => 'Controls document upload validation and storage',
            ],
            [
                'key' => 'profile.document_ownership',
                'name' => 'Document Ownership Policy',
                'settings' => [
                    'enabled' => true,
                    'strict_ownership' => true,
                    'check_user_active' => true,
                    'allow_admin_override' => false,
                ],
                'description' => 'Ensures users can only access their own documents',
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

        $this->command->info('Created ' . count($policies) . ' policy settings');
    }
}
