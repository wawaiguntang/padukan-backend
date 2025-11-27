<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Authorization\Models\Permission;
use Modules\Authorization\Models\Role;
use Modules\Authorization\Models\RolePermission;

class ProfilePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Profile module permissions...');

        // Create permissions
        $this->createPermissions();


        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Profile permissions seeded successfully!');
    }

    /**
     * Create all profile-related permissions
     */
    private function createPermissions(): void
    {
        $permissions = [
            // Customer Permissions
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
                'name' => 'customer.address.create',
                'slug' => 'customer-address-create',
                'description' => 'Create customer addresses',
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
                'description' => 'Update customer addresses',
                'resource' => 'address',
                'action' => 'update',
            ],
            [
                'name' => 'customer.address.delete',
                'slug' => 'customer-address-delete',
                'description' => 'Delete customer addresses',
                'resource' => 'address',
                'action' => 'delete',
            ],
            [
                'name' => 'customer.document.upload',
                'slug' => 'customer-document-upload',
                'description' => 'Upload customer documents',
                'resource' => 'document',
                'action' => 'upload',
            ],
            [
                'name' => 'customer.document.view',
                'slug' => 'customer-document-view',
                'description' => 'View customer documents',
                'resource' => 'document',
                'action' => 'view',
            ],
            [
                'name' => 'customer.document.update',
                'slug' => 'customer-document-update',
                'description' => 'Update customer documents',
                'resource' => 'document',
                'action' => 'update',
            ],
            [
                'name' => 'customer.document.delete',
                'slug' => 'customer-document-delete',
                'description' => 'Delete customer documents',
                'resource' => 'document',
                'action' => 'delete',
            ],
            [
                'name' => 'customer.document.resubmit',
                'slug' => 'customer-document-resubmit',
                'description' => 'Resubmit customer documents',
                'resource' => 'document',
                'action' => 'resubmit',
            ],

            // Driver Permissions
            [
                'name' => 'driver.profile.view',
                'slug' => 'driver-profile-view',
                'description' => 'View driver profile information',
                'resource' => 'profile',
                'action' => 'view',
            ],
            [
                'name' => 'driver.profile.update',
                'slug' => 'driver-profile-update',
                'description' => 'Update driver profile information',
                'resource' => 'profile',
                'action' => 'update',
            ],
            [
                'name' => 'driver.vehicle.create',
                'slug' => 'driver-vehicle-create',
                'description' => 'Create driver vehicles',
                'resource' => 'vehicle',
                'action' => 'create',
            ],
            [
                'name' => 'driver.vehicle.view',
                'slug' => 'driver-vehicle-view',
                'description' => 'View driver vehicles',
                'resource' => 'vehicle',
                'action' => 'view',
            ],
            [
                'name' => 'driver.vehicle.update',
                'slug' => 'driver-vehicle-update',
                'description' => 'Update driver vehicles',
                'resource' => 'vehicle',
                'action' => 'update',
            ],
            [
                'name' => 'driver.vehicle.delete',
                'slug' => 'driver-vehicle-delete',
                'description' => 'Delete driver vehicles',
                'resource' => 'vehicle',
                'action' => 'delete',
            ],
            [
                'name' => 'driver.document.upload',
                'slug' => 'driver-document-upload',
                'description' => 'Upload driver documents',
                'resource' => 'document',
                'action' => 'upload',
            ],
            [
                'name' => 'driver.document.view',
                'slug' => 'driver-document-view',
                'description' => 'View driver documents',
                'resource' => 'document',
                'action' => 'view',
            ],
            [
                'name' => 'driver.document.update',
                'slug' => 'driver-document-update',
                'description' => 'Update driver documents',
                'resource' => 'document',
                'action' => 'update',
            ],
            [
                'name' => 'driver.document.delete',
                'slug' => 'driver-document-delete',
                'description' => 'Delete driver documents',
                'resource' => 'document',
                'action' => 'delete',
            ],
            [
                'name' => 'driver.document.resubmit',
                'slug' => 'driver-document-resubmit',
                'description' => 'Resubmit driver documents',
                'resource' => 'document',
                'action' => 'resubmit',
            ],
            [
                'name' => 'driver.verification.request',
                'slug' => 'driver-verification-request',
                'description' => 'Request driver verification',
                'resource' => 'verification',
                'action' => 'request',
            ],
            [
                'name' => 'driver.verification.view',
                'slug' => 'driver-verification-view',
                'description' => 'View driver verification status',
                'resource' => 'verification',
                'action' => 'view',
            ],

            // Merchant Permissions
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
                'name' => 'merchant.business_address.view',
                'slug' => 'merchant-business-address-view',
                'description' => 'View merchant business address',
                'resource' => 'business_address',
                'action' => 'view',
            ],
            [
                'name' => 'merchant.business_address.update',
                'slug' => 'merchant-business-address-update',
                'description' => 'Update merchant business address',
                'resource' => 'business_address',
                'action' => 'update',
            ],
            [
                'name' => 'merchant.bank.create',
                'slug' => 'merchant-bank-create',
                'description' => 'Create merchant bank accounts',
                'resource' => 'bank',
                'action' => 'create',
            ],
            [
                'name' => 'merchant.bank.view',
                'slug' => 'merchant-bank-view',
                'description' => 'View merchant bank accounts',
                'resource' => 'bank',
                'action' => 'view',
            ],
            [
                'name' => 'merchant.bank.update',
                'slug' => 'merchant-bank-update',
                'description' => 'Update merchant bank accounts',
                'resource' => 'bank',
                'action' => 'update',
            ],
            [
                'name' => 'merchant.bank.delete',
                'slug' => 'merchant-bank-delete',
                'description' => 'Delete merchant bank accounts',
                'resource' => 'bank',
                'action' => 'delete',
            ],
            [
                'name' => 'merchant.document.upload',
                'slug' => 'merchant-document-upload',
                'description' => 'Upload merchant documents',
                'resource' => 'document',
                'action' => 'upload',
            ],
            [
                'name' => 'merchant.document.view',
                'slug' => 'merchant-document-view',
                'description' => 'View merchant documents',
                'resource' => 'document',
                'action' => 'view',
            ],
            [
                'name' => 'merchant.document.update',
                'slug' => 'merchant-document-update',
                'description' => 'Update merchant documents',
                'resource' => 'document',
                'action' => 'update',
            ],
            [
                'name' => 'merchant.document.delete',
                'slug' => 'merchant-document-delete',
                'description' => 'Delete merchant documents',
                'resource' => 'document',
                'action' => 'delete',
            ],
            [
                'name' => 'merchant.document.resubmit',
                'slug' => 'merchant-document-resubmit',
                'description' => 'Resubmit merchant documents',
                'resource' => 'document',
                'action' => 'resubmit',
            ],
            [
                'name' => 'merchant.verification.request',
                'slug' => 'merchant-verification-request',
                'description' => 'Request merchant verification',
                'resource' => 'verification',
                'action' => 'request',
            ],
            [
                'name' => 'merchant.verification.view',
                'slug' => 'merchant-verification-view',
                'description' => 'View merchant verification status',
                'resource' => 'verification',
                'action' => 'view',
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                array_merge($permissionData, ['is_active' => true])
            );
        }

        $this->command->info('Created ' . count($permissions) . ' permissions');
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        $rolePermissions = [
            'customer' => [
                'customer.profile.view',
                'customer.profile.update',
                'customer.address.create',
                'customer.address.view',
                'customer.address.update',
                'customer.address.delete',
                'customer.document.upload',
                'customer.document.view',
                'customer.document.update',
                'customer.document.delete',
                'customer.document.resubmit',
            ],
            'driver' => [
                'driver.profile.view',
                'driver.profile.update',
                'driver.vehicle.create',
                'driver.vehicle.view',
                'driver.vehicle.update',
                'driver.vehicle.delete',
                'driver.document.upload',
                'driver.document.view',
                'driver.document.update',
                'driver.document.delete',
                'driver.document.resubmit',
                'driver.verification.request',
                'driver.verification.view',
            ],
            'merchant' => [
                'merchant.profile.view',
                'merchant.profile.update',
                'merchant.business_address.view',
                'merchant.business_address.update',
                'merchant.bank.create',
                'merchant.bank.view',
                'merchant.bank.update',
                'merchant.bank.delete',
                'merchant.document.upload',
                'merchant.document.view',
                'merchant.document.update',
                'merchant.document.delete',
                'merchant.document.resubmit',
                'merchant.verification.request',
                'merchant.verification.view',
            ],
            // Note: Admin and super-admin permissions will be handled in separate admin module
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
