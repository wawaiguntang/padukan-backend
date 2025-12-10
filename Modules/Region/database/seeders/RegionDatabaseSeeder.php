<?php

namespace Modules\Region\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Region\Models\Region;

class RegionDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lampungPolygon = [
            [-6.1, 106.7],
            [-6.1, 106.9],
            [-6.3, 106.9],
            [-6.3, 106.7],
            [-6.1, 106.7],
        ];

        $lampung = Region::create([
            'name' => 'Lampung',
            'slug' => 'lampung',
            'polygon' => json_encode($lampungPolygon),
            'timezone' => 'Asia/Jakarta',
            'currency_code' => 'IDR',
            'is_active' => true,
        ]);

        $lampung->services()->createMany([
            ['service_name' => 'food', 'is_active' => true],
            ['service_name' => 'ride', 'is_active' => true],
            ['service_name' => 'car', 'is_active' => true],
            ['service_name' => 'mart', 'is_active' => true],
            ['service_name' => 'send', 'is_active' => true],
            ['service_name' => 'service', 'is_active' => false],
        ]);
    }
}
