<?php

namespace App\Shared\Tax;

interface ITaxService
{
    /**
     * Calculate tax for any amount with flexible context.
     * Handles all tax types: compound, inclusive, exclusive, fixed amounts.
     *
     * @param float $amount The amount to calculate tax for
     * @param array $context Flexible context array containing:
     *   - merchant_id: string (merchant-specific taxes)
     *   - organization_id: string (organization inheritance)
     *   - region_id: string (regional taxes)
     *   - product_id: string (product-specific taxes)
     *   - customer_id: string (customer exemptions)
     *   - transaction_date: string (historical tax rates)
     *   - is_inclusive: bool (inclusive tax flag)
     *   - include_handling_fee: bool (fixed fee inclusion)
     * @return TaxResult Complete tax calculation result
     */
    public function calculateTax(float $amount, array $context = []): TaxResult;

    /**
     * Quick tax preview for UI/estimates.
     * Faster than full calculation, returns basic tax info.
     *
     * @param float $amount The amount to preview tax for
     * @param array $context Same context as calculateTax
     * @return TaxPreview Quick tax estimate
     */
    public function previewTax(float $amount, array $context = []): TaxPreview;
}
