<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Authorization\Models\Permission;
use Modules\Authorization\Models\Role;
use Modules\Authorization\Models\RolePermission;

class CustomerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Customer module permissions...');

        // Create permissions
        $this->createPermissions();

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Customer permissions seeded successfully!');
    }

    /**
     * Create all customer-related permissions
     */
    private function createPermissions(): void
    {
        $permissions = [
            // Customer Profile Permissions
            [
                'name' => 'customer.profile.view',
                'slug' => 'customer-profile-view',
                'description' => 'View customer profile information',
                'resource' => 'profile',
                'action' => 'view',
            ],
            [
                'name' => 'customer.profile.update',
                'slug' => 'customer-profile-update',
                'description' => 'Update customer profile information',
                'resource' => 'profile',
                'action' => 'update',
            ],
            [
                'name' => 'customer.profile.submit_verification',
                'slug' => 'customer-profile-submit-verification',
                'description' => 'Submit profile verification request with ID card',
                'resource' => 'profile',
                'action' => 'submit_verification',
            ],
            [
                'name' => 'customer.profile.resubmit_verification',
                'slug' => 'customer-profile-resubmit-verification',
                'description' => 'Resubmit profile verification if rejected',
                'resource' => 'profile',
                'action' => 'resubmit_verification',
            ],
            [
                'name' => 'customer.profile.check_verification_status',
                'slug' => 'customer-profile-check-verification-status',
                'description' => 'Check profile verification status',
                'resource' => 'profile',
                'action' => 'check_verification_status',
            ],

            // Customer Address Permissions
            [
                'name' => 'customer.address.create',
                'slug' => 'customer-address-create',
                'description' => 'Create new customer address',
                'resource' => 'address',
                'action' => 'create',
            ],
            [
                'name' => 'customer.address.view',
                'slug' => 'customer-address-view',
                'description' => 'View customer addresses',
                'resource' => 'address',
                'action' => 'view',
            ],
            [
                'name' => 'customer.address.update',
                'slug' => 'customer-address-update',
                'description' => 'Update customer address',
                'resource' => 'address',
                'action' => 'update',
            ],
            [
                'name' => 'customer.address.delete',
                'slug' => 'customer-address-delete',
                'description' => 'Delete customer address',
                'resource' => 'address',
                'action' => 'delete',
            ],
            [
                'name' => 'customer.address.set_primary',
                'slug' => 'customer-address-set-primary',
                'description' => 'Set primary address',
                'resource' => 'address',
                'action' => 'set_primary',
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                array_merge($permissionData, ['is_active' => true])
            );
        }

        $this->command->info('Created ' . count($permissions) . ' customer permissions');
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        $rolePermissions = [
            'customer' => [
                // Profile permissions
                'customer.profile.view',
                'customer.profile.update',
                'customer.profile.submit_verification',
                'customer.profile.resubmit_verification',
                'customer.profile.check_verification_status',

                // Address permissions
                'customer.address.create',
                'customer.address.view',
                'customer.address.update',
                'customer.address.delete',
                'customer.address.set_primary',
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
