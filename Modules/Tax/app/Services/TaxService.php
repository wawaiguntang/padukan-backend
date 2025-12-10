<?php

namespace Modules\Tax\Services;

use App\Shared\Tax\ITaxService;
use Modules\Tax\Models\TaxRule;
use Illuminate\Support\Facades\Cache;

class TaxService implements ITaxService
{
    /**
     * Calculate tax for a product price
     */
    public function calculateTax(float $price, ?string $regionId = null, ?string $categoryId = null, ?string $taxGroupId = null): float
    {
        $rules = $this->getTaxRules($regionId, $categoryId, $taxGroupId);
        $totalTax = 0;

        foreach ($rules as $rule) {
            if ($rule['type'] === 'percentage') {
                $totalTax += $price * ($rule['rate'] / 100);
            } else {
                $totalTax += $rule['rate'];
            }
        }

        return $totalTax;
    }

    /**
     * Get applicable tax rules with caching
     */
    public function getTaxRules(?string $regionId = null, ?string $categoryId = null, ?string $taxGroupId = null): array
    {
        $cacheKey = "tax_rules:region:{$regionId}:category:{$categoryId}:group:{$taxGroupId}";

        return Cache::remember($cacheKey, 3600, function () use ($regionId, $categoryId, $taxGroupId) {
            $query = TaxRule::where('is_active', true);

            // Filter by Region (Specific region OR Global rules)
            $query->where(function ($q) use ($regionId) {
                if ($regionId) {
                    $q->where('region_id', $regionId);
                }
                $q->orWhereNull('region_id');
            });

            // Filter by Category (Specific category OR All categories)
            $query->where(function ($q) use ($categoryId) {
                if ($categoryId) {
                    $q->where('category_id', $categoryId);
                }
                $q->orWhereNull('category_id');
            });

            // Filter by Tax Group (Specific group OR No group requirement)
            if ($taxGroupId) {
                $query->where(function ($q) use ($taxGroupId) {
                    $q->where('tax_group_id', $taxGroupId)
                        ->orWhereNull('tax_group_id');
                });
            } else {
                $query->whereNull('tax_group_id');
            }

            return $query->orderBy('priority', 'desc')->get()->toArray();
        });
    }
}
