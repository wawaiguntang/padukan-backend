<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Authorization\Models\Permission;
use Modules\Authorization\Models\Role;
use Modules\Authorization\Models\RolePermission;

class MerchantPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Merchant module permissions...');

        // Create permissions
        $this->createPermissions();

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Merchant permissions seeded successfully!');
    }

    /**
     * Create all merchant-related permissions
     */
    private function createPermissions(): void
    {
        $permissions = [
            // Merchant Profile Permissions
            [
                'name' => 'merchant.profile.view',
                'slug' => 'merchant-profile-view',
                'description' => 'View merchant profile information',
                'resource' => 'profile',
                'action' => 'view',
            ],
            [
                'name' => 'merchant.profile.update',
                'slug' => 'merchant-profile-update',
                'description' => 'Update merchant profile information',
                'resource' => 'profile',
                'action' => 'update',
            ],
            [
                'name' => 'merchant.profile.submit_verification',
                'slug' => 'merchant-profile-submit-verification',
                'description' => 'Submit profile verification request with ID card',
                'resource' => 'profile',
                'action' => 'submit_verification',
            ],
            [
                'name' => 'merchant.profile.resubmit_verification',
                'slug' => 'merchant-profile-resubmit-verification',
                'description' => 'Resubmit profile verification if rejected',
                'resource' => 'profile',
                'action' => 'resubmit_verification',
            ],
            [
                'name' => 'merchant.profile.check_verification_status',
                'slug' => 'merchant-profile-check-verification-status',
                'description' => 'Check profile verification status',
                'resource' => 'profile',
                'action' => 'check_verification_status',
            ],

            // Merchant Management Permissions
            [
                'name' => 'merchant.merchant.view',
                'slug' => 'merchant-merchant-view',
                'description' => 'View merchant information',
                'resource' => 'merchant',
                'action' => 'view',
            ],
            [
                'name' => 'merchant.merchant.create',
                'slug' => 'merchant-merchant-create',
                'description' => 'Create new merchant',
                'resource' => 'merchant',
                'action' => 'create',
            ],
            [
                'name' => 'merchant.merchant.update',
                'slug' => 'merchant-merchant-update',
                'description' => 'Update merchant information',
                'resource' => 'merchant',
                'action' => 'update',
            ],
            [
                'name' => 'merchant.merchant.delete',
                'slug' => 'merchant-merchant-delete',
                'description' => 'Delete merchant',
                'resource' => 'merchant',
                'action' => 'delete',
            ],
            [
                'name' => 'merchant.merchant.submit_verification',
                'slug' => 'merchant-merchant-submit-verification',
                'description' => 'Submit merchant verification request',
                'resource' => 'merchant',
                'action' => 'submit_verification',
            ],
            [
                'name' => 'merchant.merchant.check_verification_status',
                'slug' => 'merchant-merchant-check-verification-status',
                'description' => 'Check merchant verification status',
                'resource' => 'merchant',
                'action' => 'check_verification_status',
            ],
            [
                'name' => 'merchant.merchant.verification.submit',
                'slug' => 'merchant-merchant-verification-submit',
                'description' => 'Submit merchant verification',
                'resource' => 'merchant',
                'action' => 'verification_submit',
            ],
            [
                'name' => 'merchant.merchant.verification.resubmit',
                'slug' => 'merchant-merchant-verification-resubmit',
                'description' => 'Resubmit merchant verification',
                'resource' => 'merchant',
                'action' => 'verification_resubmit',
            ],
            [
                'name' => 'merchant.merchant.verification.view',
                'slug' => 'merchant-merchant-verification-view',
                'description' => 'View merchant verification status',
                'resource' => 'merchant',
                'action' => 'verification_view',
            ],
            [
                'name' => 'merchant.merchant.status.update',
                'slug' => 'merchant-merchant-status-update',
                'description' => 'Update merchant status (open/closed)',
                'resource' => 'merchant',
                'action' => 'status_update',
            ],

            // Merchant Document Permissions
            [
                'name' => 'merchant.document.view',
                'slug' => 'merchant-document-view',
                'description' => 'View merchant documents',
                'resource' => 'document',
                'action' => 'view',
            ],
            [
                'name' => 'merchant.document.upload',
                'slug' => 'merchant-document-upload',
                'description' => 'Upload merchant documents',
                'resource' => 'document',
                'action' => 'upload',
            ],
            [
                'name' => 'merchant.document.delete',
                'slug' => 'merchant-document-delete',
                'description' => 'Delete merchant documents',
                'resource' => 'document',
                'action' => 'delete',
            ],

            // Merchant Address Permissions
            [
                'name' => 'merchant.address.view',
                'slug' => 'merchant-address-view',
                'description' => 'View merchant addresses',
                'resource' => 'address',
                'action' => 'view',
            ],
            [
                'name' => 'merchant.address.create',
                'slug' => 'merchant-address-create',
                'description' => 'Create merchant addresses',
                'resource' => 'address',
                'action' => 'create',
            ],
            [
                'name' => 'merchant.address.update',
                'slug' => 'merchant-address-update',
                'description' => 'Update merchant addresses',
                'resource' => 'address',
                'action' => 'update',
            ],
            [
                'name' => 'merchant.address.delete',
                'slug' => 'merchant-address-delete',
                'description' => 'Delete merchant addresses',
                'resource' => 'address',
                'action' => 'delete',
            ],

            // Merchant Schedule Permissions
            [
                'name' => 'merchant.schedule.view',
                'slug' => 'merchant-schedule-view',
                'description' => 'View merchant schedules',
                'resource' => 'schedule',
                'action' => 'view',
            ],
            [
                'name' => 'merchant.schedule.update',
                'slug' => 'merchant-schedule-update',
                'description' => 'Update merchant schedules',
                'resource' => 'schedule',
                'action' => 'update',
            ],

            // Merchant Settings Permissions
            [
                'name' => 'merchant.setting.view',
                'slug' => 'merchant-setting-view',
                'description' => 'View merchant settings',
                'resource' => 'setting',
                'action' => 'view',
            ],
            [
                'name' => 'merchant.setting.update',
                'slug' => 'merchant-setting-update',
                'description' => 'Update merchant settings',
                'resource' => 'setting',
                'action' => 'update',
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                array_merge($permissionData, ['is_active' => true])
            );
        }

        $this->command->info('Created ' . count($permissions) . ' merchant permissions');
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        $rolePermissions = [
            'merchant' => [
                // Profile permissions
                'merchant.profile.view',
                'merchant.profile.update',
                'merchant.profile.submit_verification',
                'merchant.profile.resubmit_verification',
                'merchant.profile.check_verification_status',

                // Merchant permissions
                'merchant.merchant.view',
                'merchant.merchant.create',
                'merchant.merchant.update',
                'merchant.merchant.delete',
                'merchant.merchant.submit_verification',
                'merchant.merchant.check_verification_status',
                'merchant.merchant.verification.submit',
                'merchant.merchant.verification.resubmit',
                'merchant.merchant.verification.view',
                'merchant.merchant.status.update',

                // Document permissions
                'merchant.document.view',
                'merchant.document.upload',
                'merchant.document.delete',

                // Address permissions
                'merchant.address.view',
                'merchant.address.create',
                'merchant.address.update',
                'merchant.address.delete',

                // Schedule permissions
                'merchant.schedule.view',
                'merchant.schedule.update',

                // Settings permissions
                'merchant.setting.view',
                'merchant.setting.update',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::where('slug', $roleName)->first();
            if (!$role) {
                $this->command->error("Role '{$roleName}' not found, skipping permission assignment");
                continue;
            }

            foreach ($permissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if (!$permission) {
                    $this->command->error("Permission '{$permissionName}' not found, skipping assignment to role '{$roleName}'");
                    continue;
                }

                RolePermission::firstOrCreate([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                ]);
            }

            $this->command->info("Assigned " . count($permissions) . " permissions to role '{$roleName}'");
        }
    }
}
