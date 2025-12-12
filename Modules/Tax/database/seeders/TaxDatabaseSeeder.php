<?php

namespace Modules\Tax\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Tax\Models\Tax;
use Modules\Tax\Models\TaxGroup;
use Modules\Tax\Models\TaxRate;
use Modules\Tax\Models\TaxAssignment;

class TaxDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding complex tax scenarios...');

        // Clear existing data
        $this->clearExistingData();

        // Seed complex tax scenarios
        $this->seedSystemTaxes();
        $this->seedRestaurantTaxes();
        $this->seedRetailTaxes();
        $this->seedHotelTaxes();
        $this->seedRegionalVariations();
        $this->seedMerchantSpecificTaxes();
        $this->seedFranchiseTaxes();

        $this->command->info('Complex tax scenarios seeded successfully!');
    }

    private function clearExistingData(): void
    {
        DB::table('tax_assignments')->delete();
        DB::table('tax_rates')->delete();
        DB::table('tax_groups')->delete();
        DB::table('taxes')->delete();
    }

    /**
     * Seed system/global taxes (Indonesian tax system)
     */
    private function seedSystemTaxes(): void
    {
        $this->command->info('Seeding system taxes...');

        // PPN (Value Added Tax) - 11%
        $ppn = Tax::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'PPN',
            'slug' => 'ppn',
            'description' => 'Pajak Pertambahan Nilai 11%',
            'is_active' => true,
        ]);

        $ppnGroup = TaxGroup::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'National Taxes',
            'description' => 'System-wide national tax obligations',
            'is_active' => true,
        ]);

        TaxRate::create([
            'id' => Str::uuid(),
            'tax_group_id' => $ppnGroup->id,
            'tax_id' => $ppn->id,
            'rate' => 11.00,
            'type' => 'percentage',
            'is_inclusive' => false,
            'priority' => 1,
            'based_on' => 'subtotal',
            'valid_from' => now()->subYears(2),
            'valid_until' => null,
        ]);

        // Assign to all regions (global)
        TaxAssignment::create([
            'id' => Str::uuid(),
            'tax_group_id' => $ppnGroup->id,
            'assignable_type' => 'region',
            'assignable_id' => 'all', // Special case for global
        ]);
    }

    /**
     * Seed restaurant-specific tax scenarios
     */
    private function seedRestaurantTaxes(): void
    {
        $this->command->info('Seeding restaurant taxes...');

        // Restaurant Service Charge
        $serviceCharge = Tax::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'Service Charge',
            'slug' => 'service-charge',
            'description' => 'Restaurant service charge',
            'is_active' => true,
        ]);

        $restaurantGroup = TaxGroup::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'Restaurant Services',
            'description' => 'Taxes for restaurant and food service businesses',
            'is_active' => true,
        ]);

        TaxRate::create([
            'id' => Str::uuid(),
            'tax_group_id' => $restaurantGroup->id,
            'tax_id' => $serviceCharge->id,
            'rate' => 5.00,
            'type' => 'percentage',
            'is_inclusive' => false,
            'priority' => 2,
            'based_on' => 'total_after_previous_tax',
            'valid_from' => now()->subYears(1),
            'valid_until' => null,
        ]);

        // Assign to food categories
        TaxAssignment::create([
            'id' => Str::uuid(),
            'tax_group_id' => $restaurantGroup->id,
            'assignable_type' => 'category',
            'assignable_id' => 'food',
        ]);

        TaxAssignment::create([
            'id' => Str::uuid(),
            'tax_group_id' => $restaurantGroup->id,
            'assignable_type' => 'category',
            'assignable_id' => 'beverage',
        ]);
    }

    /**
     * Seed retail tax scenarios with price tiers
     */
    private function seedRetailTaxes(): void
    {
        $this->command->info('Seeding retail taxes...');

        // Luxury Tax for high-value items
        $luxuryTax = Tax::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'Luxury Tax',
            'slug' => 'luxury-tax',
            'description' => 'Tax for luxury goods',
            'is_active' => true,
        ]);

        $luxuryGroup = TaxGroup::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'Luxury Goods Tax',
            'description' => 'Additional tax for luxury items',
            'is_active' => true,
        ]);

        // Tiered luxury tax rates
        TaxRate::create([
            'id' => Str::uuid(),
            'tax_group_id' => $luxuryGroup->id,
            'tax_id' => $luxuryTax->id,
            'rate' => 10.00,
            'type' => 'percentage',
            'is_inclusive' => false,
            'priority' => 3,
            'based_on' => 'subtotal',
            'valid_from' => now()->subYears(1),
            'valid_until' => null,
            'min_price' => 5000000, // 5 million IDR
            'max_price' => null,
        ]);

        TaxAssignment::create([
            'id' => Str::uuid(),
            'tax_group_id' => $luxuryGroup->id,
            'assignable_type' => 'category',
            'assignable_id' => 'luxury',
        ]);
    }

    /**
     * Seed hotel tax scenarios
     */
    private function seedHotelTaxes(): void
    {
        $this->command->info('Seeding hotel taxes...');

        // Hotel Tax (Local government tax)
        $hotelTax = Tax::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'Hotel Tax',
            'slug' => 'hotel-tax',
            'description' => 'Local hotel accommodation tax',
            'is_active' => true,
        ]);

        $hotelGroup = TaxGroup::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'Hotel Accommodation Tax',
            'description' => 'Taxes for hotel and accommodation services',
            'is_active' => true,
        ]);

        TaxRate::create([
            'id' => Str::uuid(),
            'tax_group_id' => $hotelGroup->id,
            'tax_id' => $hotelTax->id,
            'rate' => 10000, // Fixed amount per room per night
            'type' => 'fixed',
            'is_inclusive' => false,
            'priority' => 1,
            'based_on' => 'subtotal',
            'valid_from' => now()->subYears(1),
            'valid_until' => null,
        ]);

        TaxAssignment::create([
            'id' => Str::uuid(),
            'tax_group_id' => $hotelGroup->id,
            'assignable_type' => 'category',
            'assignable_id' => 'accommodation',
        ]);
    }

    /**
     * Seed regional tax variations
     */
    private function seedRegionalVariations(): void
    {
        $this->command->info('Seeding regional variations...');

        // Jakarta Local Tax
        $jakartaTax = Tax::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'Jakarta Local Tax',
            'slug' => 'jakarta-local-tax',
            'description' => 'Special local tax for Jakarta',
            'is_active' => true,
        ]);

        $jakartaGroup = TaxGroup::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'Jakarta Regional Taxes',
            'description' => 'Regional taxes specific to Jakarta',
            'is_active' => true,
        ]);

        TaxRate::create([
            'id' => Str::uuid(),
            'tax_group_id' => $jakartaGroup->id,
            'tax_id' => $jakartaTax->id,
            'rate' => 0.3,
            'type' => 'percentage',
            'is_inclusive' => false,
            'priority' => 4,
            'based_on' => 'total_after_previous_tax',
            'valid_from' => now()->subYears(1),
            'valid_until' => null,
        ]);

        TaxAssignment::create([
            'id' => Str::uuid(),
            'tax_group_id' => $jakartaGroup->id,
            'assignable_type' => 'region',
            'assignable_id' => 'jakarta',
        ]);

        // Surabaya Local Tax
        $surabayaTax = Tax::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'Surabaya Local Tax',
            'slug' => 'surabaya-local-tax',
            'description' => 'Special local tax for Surabaya',
            'is_active' => true,
        ]);

        $surabayaGroup = TaxGroup::create([
            'id' => Str::uuid(),
            'owner_type' => 'system',
            'owner_id' => null,
            'name' => 'Surabaya Regional Taxes',
            'description' => 'Regional taxes specific to Surabaya',
            'is_active' => true,
        ]);

        TaxRate::create([
            'id' => Str::uuid(),
            'tax_group_id' => $surabayaGroup->id,
            'tax_id' => $surabayaTax->id,
            'rate' => 0.2,
            'type' => 'percentage',
            'is_inclusive' => false,
            'priority' => 4,
            'based_on' => 'total_after_previous_tax',
            'valid_from' => now()->subYears(1),
            'valid_until' => null,
        ]);

        TaxAssignment::create([
            'id' => Str::uuid(),
            'tax_group_id' => $surabayaGroup->id,
            'assignable_type' => 'region',
            'assignable_id' => 'surabaya',
        ]);
    }

    /**
     * Seed merchant-specific taxes
     */
    private function seedMerchantSpecificTaxes(): void
    {
        $this->command->info('Seeding merchant-specific taxes...');

        // Merchant A specific tax
        $merchantATax = Tax::create([
            'id' => Str::uuid(),
            'owner_type' => 'merchant',
            'owner_id' => 'merchant-a-uuid', // Mock merchant ID
            'name' => 'Merchant A Special Fee',
            'slug' => 'merchant-a-special-fee',
            'description' => 'Special fee for Merchant A',
            'is_active' => true,
        ]);

        $merchantAGroup = TaxGroup::create([
            'id' => Str::uuid(),
            'owner_type' => 'merchant',
            'owner_id' => 'merchant-a-uuid',
            'name' => 'Merchant A Fees',
            'description' => 'Special fees for Merchant A operations',
            'is_active' => true,
        ]);

        TaxRate::create([
            'id' => Str::uuid(),
            'tax_group_id' => $merchantAGroup->id,
            'tax_id' => $merchantATax->id,
            'rate' => 2.00,
            'type' => 'percentage',
            'is_inclusive' => false,
            'priority' => 5,
            'based_on' => 'total_after_previous_tax',
            'valid_from' => now()->subYears(1),
            'valid_until' => null,
        ]);

        // Assign to Merchant A's products
        TaxAssignment::create([
            'id' => Str::uuid(),
            'tax_group_id' => $merchantAGroup->id,
            'assignable_type' => 'merchant',
            'assignable_id' => 'merchant-a-uuid',
        ]);
    }

    /**
     * Seed franchise tax scenarios
     */
    private function seedFranchiseTaxes(): void
    {
        $this->command->info('Seeding franchise taxes...');

        // Franchise royalty fee
        $royaltyTax = Tax::create([
            'id' => Str::uuid(),
            'owner_type' => 'franchise',
            'owner_id' => 'franchise-alpha-uuid', // Mock franchise ID
            'name' => 'Franchise Royalty',
            'slug' => 'franchise-royalty',
            'description' => 'Franchise royalty fee',
            'is_active' => true,
        ]);

        $franchiseGroup = TaxGroup::create([
            'id' => Str::uuid(),
            'owner_type' => 'franchise',
            'owner_id' => 'franchise-alpha-uuid',
            'name' => 'Franchise Fees',
            'description' => 'Franchise-related fees and royalties',
            'is_active' => true,
        ]);

        TaxRate::create([
            'id' => Str::uuid(),
            'tax_group_id' => $franchiseGroup->id,
            'tax_id' => $royaltyTax->id,
            'rate' => 8.00,
            'type' => 'percentage',
            'is_inclusive' => false,
            'priority' => 6,
            'based_on' => 'subtotal',
            'valid_from' => now()->subYears(1),
            'valid_until' => null,
        ]);

        TaxAssignment::create([
            'id' => Str::uuid(),
            'tax_group_id' => $franchiseGroup->id,
            'assignable_type' => 'franchise',
            'assignable_id' => 'franchise-alpha-uuid',
        ]);
    }
}
