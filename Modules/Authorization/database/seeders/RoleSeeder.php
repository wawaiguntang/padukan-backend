<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
}
