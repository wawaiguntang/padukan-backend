<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Super administrator with full access to all system features',
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user with basic access',
            ],
            [
                'name' => 'Driver',
                'slug' => 'driver',
                'description' => 'Driver role for delivery and transportation services',
            ],
            [
                'name' => 'Merchant',
                'slug' => 'merchant',
                'description' => 'Merchant role for business owners and sellers',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
