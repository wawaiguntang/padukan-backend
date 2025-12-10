<?php

namespace App\Shared\Tax;

interface ITaxService
{
    /**
     * Calculate tax for a product price
     *
     * @param float $price The product price
     * @param string|null $regionId The region ID (if any)
     * @param string|null $categoryId The product category ID (if any)
     * @param string|null $taxGroupId The tax group ID (if any)
     * @return float The calculated tax amount
     */
    public function calculateTax(float $price, ?string $regionId = null, ?string $categoryId = null, ?string $taxGroupId = null): float;

    /**
     * Get applicable tax rules
     *
     * @param string|null $regionId
     * @param string|null $categoryId
     * @param string|null $taxGroupId
     * @return array List of applied rules
     */
    public function getTaxRules(?string $regionId = null, ?string $categoryId = null, ?string $taxGroupId = null): array;
}
