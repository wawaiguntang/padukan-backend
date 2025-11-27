<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthorizationEngineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed roles
        // $this->seedRoles();

        // Seed permissions
        // $this->seedPermissions();

        // Seed role permissions
        // $this->seedRolePermissions();

        // Seed policy settings
        // $this->seedPolicySettings();

        // Seed Profile module permissions and policies
        $this->call(ProfilePermissionSeeder::class);
        $this->call(ProfilePolicySeeder::class);
    }

    private function seedRoles(): void
    {
        $roles = [
            [
                'id' => Str::uuid(),
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Admin Area',
                'slug' => 'admin-area',
                'description' => 'Regional administrator with limited system access'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Basic authenticated user'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Customer who can order services'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Driver',
                'slug' => 'driver',
                'description' => 'Driver who can accept and complete orders'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Merchant',
                'slug' => 'merchant',
                'description' => 'Merchant who can manage their business'
            ]
        ];

        DB::table('roles')->insert($roles);
    }

    private function seedPermissions(): void
    {
        $permissions = [
            // Order permissions
            ['id' => Str::uuid(), 'name' => 'Create Order', 'slug' => 'orders.create', 'resource' => 'orders', 'action' => 'create'],
            ['id' => Str::uuid(), 'name' => 'Read Order', 'slug' => 'orders.read', 'resource' => 'orders', 'action' => 'read'],
            ['id' => Str::uuid(), 'name' => 'Update Order', 'slug' => 'orders.update', 'resource' => 'orders', 'action' => 'update'],
            ['id' => Str::uuid(), 'name' => 'Cancel Order', 'slug' => 'orders.cancel', 'resource' => 'orders', 'action' => 'cancel'],

            // Driver permissions
            ['id' => Str::uuid(), 'name' => 'Accept Order', 'slug' => 'orders.accept', 'resource' => 'orders', 'action' => 'accept'],
            ['id' => Str::uuid(), 'name' => 'Complete Order', 'slug' => 'orders.complete', 'resource' => 'orders', 'action' => 'complete'],
            ['id' => Str::uuid(), 'name' => 'Update Location', 'slug' => 'drivers.update_location', 'resource' => 'drivers', 'action' => 'update_location'],

            // Merchant permissions
            ['id' => Str::uuid(), 'name' => 'Update Menu', 'slug' => 'merchants.update_menu', 'resource' => 'merchants', 'action' => 'update_menu'],
            ['id' => Str::uuid(), 'name' => 'View Analytics', 'slug' => 'merchants.view_analytics', 'resource' => 'merchants', 'action' => 'view_analytics'],

            // Customer permissions
            ['id' => Str::uuid(), 'name' => 'Rate Driver', 'slug' => 'drivers.rate', 'resource' => 'drivers', 'action' => 'rate'],
            ['id' => Str::uuid(), 'name' => 'View Order History', 'slug' => 'orders.view_history', 'resource' => 'orders', 'action' => 'view_history'],

            // Admin permissions
            ['id' => Str::uuid(), 'name' => 'Manage Users', 'slug' => 'admin.manage_users', 'resource' => 'users', 'action' => 'manage'],
            ['id' => Str::uuid(), 'name' => 'View System Metrics', 'slug' => 'admin.view_metrics', 'resource' => 'system', 'action' => 'view_metrics'],
            ['id' => Str::uuid(), 'name' => 'Manage Roles', 'slug' => 'admin.manage_roles', 'resource' => 'roles', 'action' => 'manage'],
        ];

        DB::table('permissions')->insert($permissions);
    }

    private function seedRolePermissions(): void
    {
        // Get role and permission IDs
        $roles = DB::table('roles')->pluck('id', 'slug');
        $permissions = DB::table('permissions')->pluck('id', 'slug');

        $rolePermissions = [];

        // Super Admin - all permissions
        if (isset($roles['super-admin'])) {
            foreach ($permissions as $permId) {
                $rolePermissions[] = [
                    'id' => Str::uuid(),
                    'role_id' => $roles['super-admin'],
                    'permission_id' => $permId
                ];
            }
        }

        // Admin Area - limited admin permissions
        if (isset($roles['admin-area'])) {
            $adminPerms = [
                'orders.read', 'orders.update', 'admin.view_metrics',
                'drivers.update_location', 'merchants.view_analytics'
            ];
            foreach ($adminPerms as $permSlug) {
                if (isset($permissions[$permSlug])) {
                    $rolePermissions[] = [
                        'id' => Str::uuid(),
                        'role_id' => $roles['admin-area'],
                        'permission_id' => $permissions[$permSlug]
                    ];
                }
            }
        }

        // Customer permissions
        if (isset($roles['customer'])) {
            $customerPerms = [
                'orders.create', 'orders.read', 'orders.cancel',
                'orders.view_history', 'drivers.rate'
            ];
            foreach ($customerPerms as $permSlug) {
                if (isset($permissions[$permSlug])) {
                    $rolePermissions[] = [
                        'id' => Str::uuid(),
                        'role_id' => $roles['customer'],
                        'permission_id' => $permissions[$permSlug]
                    ];
                }
            }
        }

        // Driver permissions
        if (isset($roles['driver'])) {
            $driverPerms = [
                'orders.read', 'orders.accept', 'orders.complete',
                'drivers.update_location'
            ];
            foreach ($driverPerms as $permSlug) {
                if (isset($permissions[$permSlug])) {
                    $rolePermissions[] = [
                        'id' => Str::uuid(),
                        'role_id' => $roles['driver'],
                        'permission_id' => $permissions[$permSlug]
                    ];
                }
            }
        }

        // Merchant permissions
        if (isset($roles['merchant'])) {
            $merchantPerms = [
                'orders.read', 'merchants.update_menu', 'merchants.view_analytics'
            ];
            foreach ($merchantPerms as $permSlug) {
                if (isset($permissions[$permSlug])) {
                    $rolePermissions[] = [
                        'id' => Str::uuid(),
                        'role_id' => $roles['merchant'],
                        'permission_id' => $permissions[$permSlug]
                    ];
                }
            }
        }

        DB::table('role_permissions')->insert($rolePermissions);
    }

    private function seedPolicySettings(): void
    {
        $policySettings = [
            [
                'id' => Str::uuid(),
                'key' => 'driver_accept_order',
                'name' => 'Driver Can Accept Order',
                'settings' => json_encode([
                    'min_rating' => 4.0,
                    'max_distance_km' => 5,
                    'business_hours_start' => '06:00',
                    'business_hours_end' => '22:00',
                    'max_daily_orders' => 50
                ]),
                'is_active' => true,
                'description' => 'Settings for driver order acceptance policy'
            ],
            [
                'id' => Str::uuid(),
                'key' => 'customer_cancel_order',
                'name' => 'Customer Can Cancel Order',
                'settings' => json_encode([
                    'cancel_window_minutes' => 5,
                    'allowed_statuses' => ['pending', 'confirmed'],
                    'max_cancellations_per_day' => 3
                ]),
                'is_active' => true,
                'description' => 'Settings for customer order cancellation policy'
            ],
            [
                'id' => Str::uuid(),
                'key' => 'merchant_update_menu',
                'name' => 'Merchant Can Update Menu',
                'settings' => json_encode([
                    'max_items_per_update' => 10,
                    'allowed_hours_start' => '00:00',
                    'allowed_hours_end' => '23:59',
                    'requires_approval' => false
                ]),
                'is_active' => true,
                'description' => 'Settings for merchant menu update policy'
            ],
            [
                'id' => Str::uuid(),
                'key' => 'admin_system_access',
                'name' => 'Admin System Access',
                'settings' => json_encode([
                    'max_session_duration_hours' => 8,
                    'require_2fa' => true,
                    'allowed_ip_ranges' => ['192.168.0.0/16', '10.0.0.0/8']
                ]),
                'is_active' => true,
                'description' => 'Settings for admin system access policy'
            ],
            [
                'id' => Str::uuid(),
                'key' => 'customer_rate_driver',
                'name' => 'Customer Can Rate Driver',
                'settings' => json_encode([
                    'rating_window_hours' => 24,
                    'allowed_ratings' => [1, 2, 3, 4, 5],
                    'require_order_completion' => true
                ]),
                'is_active' => true,
                'description' => 'Settings for customer driver rating policy'
            ],
            [
                'id' => Str::uuid(),
                'key' => 'driver_update_location',
                'name' => 'Driver Can Update Location',
                'settings' => json_encode([
                    'update_interval_seconds' => 30,
                    'require_gps_accuracy' => 50,
                    'max_speed_kmh' => 120
                ]),
                'is_active' => true,
                'description' => 'Settings for driver location update policy'
            ]
        ];

        DB::table('policy_settings')->insert($policySettings);
    }
}