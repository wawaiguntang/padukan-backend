<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            [
                'name' => 'View Users',
                'slug' => 'view-users',
                'description' => 'Can view user list and details',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'create-users',
                'description' => 'Can create new users',
            ],
            [
                'name' => 'Edit Users',
                'slug' => 'edit-users',
                'description' => 'Can edit existing users',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'delete-users',
                'description' => 'Can delete users',
            ],

            // Order Management
            [
                'name' => 'View Orders',
                'slug' => 'view-orders',
                'description' => 'Can view orders',
            ],
            [
                'name' => 'Create Orders',
                'slug' => 'create-orders',
                'description' => 'Can create new orders',
            ],
            [
                'name' => 'Edit Orders',
                'slug' => 'edit-orders',
                'description' => 'Can edit existing orders',
            ],
            [
                'name' => 'Cancel Orders',
                'slug' => 'cancel-orders',
                'description' => 'Can cancel orders',
            ],

            // Product Management
            [
                'name' => 'View Products',
                'slug' => 'view-products',
                'description' => 'Can view products',
            ],
            [
                'name' => 'Create Products',
                'slug' => 'create-products',
                'description' => 'Can create new products',
            ],
            [
                'name' => 'Edit Products',
                'slug' => 'edit-products',
                'description' => 'Can edit existing products',
            ],
            [
                'name' => 'Delete Products',
                'slug' => 'delete-products',
                'description' => 'Can delete products',
            ],

            // Driver Permissions
            [
                'name' => 'Accept Deliveries',
                'slug' => 'accept-deliveries',
                'description' => 'Can accept delivery requests',
            ],
            [
                'name' => 'Update Delivery Status',
                'slug' => 'update-delivery-status',
                'description' => 'Can update delivery status',
            ],
            [
                'name' => 'View Delivery History',
                'slug' => 'view-delivery-history',
                'description' => 'Can view delivery history',
            ],

            // Merchant Permissions
            [
                'name' => 'Manage Store',
                'slug' => 'manage-store',
                'description' => 'Can manage store settings',
            ],
            [
                'name' => 'View Sales Reports',
                'slug' => 'view-sales-reports',
                'description' => 'Can view sales reports',
            ],
            [
                'name' => 'Process Refunds',
                'slug' => 'process-refunds',
                'description' => 'Can process refund requests',
            ],

            // System Permissions
            [
                'name' => 'View Dashboard',
                'slug' => 'view-dashboard',
                'description' => 'Can access admin dashboard',
            ],
            [
                'name' => 'Manage Settings',
                'slug' => 'manage-settings',
                'description' => 'Can manage system settings',
            ],
            [
                'name' => 'View Reports',
                'slug' => 'view-reports',
                'description' => 'Can view system reports',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
