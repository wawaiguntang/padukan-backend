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
