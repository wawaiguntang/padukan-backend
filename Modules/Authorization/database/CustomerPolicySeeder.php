<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\PolicySetting;

class CustomerPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Customer module policy settings...');

        $policies = [
            [
                'key' => 'customer.profile.ownership',
                'name' => 'Customer Profile Ownership Policy',
                'settings' => [
                    'strict_ownership' => true,
                    'check_user_active' => true,
                ],
                'description' => 'Ensures customers can only access their own profile data',
            ],
            [
                'key' => 'customer.profile.verification',
                'name' => 'Customer Profile Verification Policy',
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
                'key' => 'customer.address.ownership',
                'name' => 'Customer Address Ownership Policy',
                'settings' => [
                    'strict_ownership' => true,
                    'check_user_active' => true,
                ],
                'description' => 'Ensures customers can only access their own addresses',
            ],
            [
                'key' => 'customer.address.management',
                'name' => 'Customer Address Management Policy',
                'settings' => [
                    'max_addresses_per_customer' => 10,
                    'require_complete_address' => true,
                    'required_fields' => ['street', 'city', 'province', 'postal_code', 'latitude', 'longitude'],
                    'validate_coordinates' => true,
                    'allow_multiple_primary' => false,
                ],
                'description' => 'Manages customer address creation and validation',
            ],
            [
                'key' => 'customer.document.verification_upload',
                'name' => 'Customer Verification Document Upload Policy',
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
                'key' => 'customer.profile.avatar',
                'name' => 'Customer Profile Avatar Policy',
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
                'description' => 'Controls customer profile avatar upload and management',
            ],
            [
                'key' => 'customer.data.privacy',
                'name' => 'Customer Data Privacy Policy',
                'settings' => [
                    'gdpr_compliant' => true,
                    'data_retention_period' => 2555, // 7 years in days
                    'allow_data_export' => true,
                    'allow_data_deletion' => true,
                    'require_consent_for_marketing' => true,
                    'encrypt_personal_data' => true,
                    'anonymize_after_retention' => true,
                ],
                'description' => 'Ensures customer data privacy and GDPR compliance',
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

        $this->command->info('Created ' . count($policies) . ' customer policy settings');
    }
}
