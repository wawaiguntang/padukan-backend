<?php

namespace Modules\Authorization\Database\Seeders;

use Illuminate\Database\Seeder;

class AuthorizationDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AuthorizationEngineSeeder::class,
            DriverPermissionSeeder::class,
            DriverPolicySeeder::class,
            CustomerPermissionSeeder::class,
            CustomerPolicySeeder::class,
        ]);
    }
}
