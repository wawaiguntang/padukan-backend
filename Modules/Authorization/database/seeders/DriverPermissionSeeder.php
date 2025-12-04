<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Authorization\Models\Permission;
use Modules\Authorization\Models\Role;
use Modules\Authorization\Models\RolePermission;

class DriverPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Driver module permissions...');

        // Create permissions
        $this->createPermissions();

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Driver permissions seeded successfully!');
    }

    /**
     * Create all driver-related permissions
     */
    private function createPermissions(): void
    {
        $permissions = [
            // Driver Profile Permissions
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
                'name' => 'driver.profile.submit_verification',
                'slug' => 'driver-profile-submit-verification',
                'description' => 'Submit profile verification request with ID card',
                'resource' => 'profile',
                'action' => 'submit_verification',
            ],
            [
                'name' => 'driver.profile.resubmit_verification',
                'slug' => 'driver-profile-resubmit-verification',
                'description' => 'Resubmit profile verification if rejected',
                'resource' => 'profile',
                'action' => 'resubmit_verification',
            ],
            [
                'name' => 'driver.profile.check_verification_status',
                'slug' => 'driver-profile-check-verification-status',
                'description' => 'Check profile verification status',
                'resource' => 'profile',
                'action' => 'check_verification_status',
            ],

            // Driver Vehicle Permissions
            [
                'name' => 'driver.vehicle.register',
                'slug' => 'driver-vehicle-register',
                'description' => 'Register new vehicle',
                'resource' => 'vehicle',
                'action' => 'register',
            ],
            [
                'name' => 'driver.vehicle.view',
                'slug' => 'driver-vehicle-view',
                'description' => 'View driver vehicles',
                'resource' => 'vehicle',
                'action' => 'view',
            ],
            [
                'name' => 'driver.vehicle.submit_verification',
                'slug' => 'driver-vehicle-submit-verification',
                'description' => 'Submit vehicle verification request with documents',
                'resource' => 'vehicle',
                'action' => 'submit_verification',
            ],
            [
                'name' => 'driver.vehicle.resubmit_verification',
                'slug' => 'driver-vehicle-resubmit-verification',
                'description' => 'Resubmit vehicle verification if rejected',
                'resource' => 'vehicle',
                'action' => 'resubmit_verification',
            ],
            [
                'name' => 'driver.vehicle.check_verification_status',
                'slug' => 'driver-vehicle-check-verification-status',
                'description' => 'Check vehicle verification status',
                'resource' => 'vehicle',
                'action' => 'check_verification_status',
            ],
            [
                'name' => 'driver.vehicle.update',
                'slug' => 'driver-vehicle-update',
                'description' => 'Update vehicle information (only if rejected)',
                'resource' => 'vehicle',
                'action' => 'update',
            ],
            [
                'name' => 'driver.vehicle.delete',
                'slug' => 'driver-vehicle-delete',
                'description' => 'Delete vehicle',
                'resource' => 'vehicle',
                'action' => 'delete',
            ],

            // Driver Status & Availability Permissions
            [
                'name' => 'driver.status.view',
                'slug' => 'driver-status-view',
                'description' => 'View driver status and availability',
                'resource' => 'status',
                'action' => 'view',
            ],
            [
                'name' => 'driver.status.update',
                'slug' => 'driver-status-update',
                'description' => 'Update driver online/offline',
                'resource' => 'status',
                'action' => 'update',
            ],
            [
                'name' => 'driver.status.update_location',
                'slug' => 'driver-status-update-location',
                'description' => 'Update driver current location',
                'resource' => 'status',
                'action' => 'update_location',
            ],
            [
                'name' => 'driver.status.set_active_services',
                'slug' => 'driver-status-set-active-services',
                'description' => 'Set current active service (RIDE, FOOD, CAR, SEND, MART)',
                'resource' => 'status',
                'action' => 'set_active_services',
            ]
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                array_merge($permissionData, ['is_active' => true])
            );
        }

        $this->command->info('Created ' . count($permissions) . ' driver permissions');
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        $rolePermissions = [
            'driver' => [
                // Profile permissions
                'driver.profile.view',
                'driver.profile.update',
                'driver.profile.submit_verification',
                'driver.profile.resubmit_verification',
                'driver.profile.check_verification_status',

                // Vehicle permissions
                'driver.vehicle.register',
                'driver.vehicle.view',
                'driver.vehicle.update',
                'driver.vehicle.delete',
                'driver.vehicle.submit_verification',
                'driver.vehicle.resubmit_verification',
                'driver.vehicle.check_verification_status',

                // Status & availability permissions
                'driver.status.view',
                'driver.status.update',
                'driver.status.update_location',
                'driver.status.set_active_services',
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
