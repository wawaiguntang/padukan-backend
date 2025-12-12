<?php

namespace Modules\Tax\Services\ForShare;

use App\Shared\Tax\ITaxService;
use App\Shared\Tax\TaxResult;
use App\Shared\Tax\TaxPreview;
use Modules\Tax\Services\Tax\ITaxService as IInternalTaxService;
use Illuminate\Support\Facades\Log;

class TaxService implements ITaxService
{
    private IInternalTaxService $internalTaxService;

    public function __construct(IInternalTaxService $internalTaxService)
    {
        $this->internalTaxService = $internalTaxService;
    }

    /**
     * Calculate tax for any amount with flexible context.
     * This is the main method - all complex logic is in internal TaxService.
     */
    public function calculateTax(float $amount, array $context = []): TaxResult
    {
        try {
            // Delegate to internal service with enhanced context processing
            $result = $this->internalTaxService->calculateTaxForOwner(
                $amount,
                $this->determineOwnerType($context),
                $this->determineOwnerId($context),
                $context
            );

            return TaxResult::fromArray([
                'originalAmount' => $result['base_amount'],
                'totalTax' => $result['total_tax'],
                'finalAmount' => $result['grand_total'],
                'taxBreakdown' => $result['taxes'],
                'isInclusive' => $this->hasInclusiveTax($result['taxes']),
                'appliedRules' => array_column($result['taxes'], 'id'),
                'calculationType' => $this->determineCalculationType($result['taxes'])
            ]);
        } catch (\Exception $e) {
            Log::error('Tax calculation failed', [
                'amount' => $amount,
                'context' => $context,
                'error' => $e->getMessage()
            ]);

            // Return zero tax result on error
            return new TaxResult(
                originalAmount: $amount,
                totalTax: 0,
                finalAmount: $amount,
                taxBreakdown: [],
                appliedRules: [],
                calculationType: 'error'
            );
        }
    }

    /**
     * Get applicable tax rules for given context without calculating amounts.
     * Useful for displaying available taxes or validation.
     *
     * @param array $context Same context as calculateTax
     * @return array List of applicable tax rules
     */
    public function getTaxRules(array $context = []): array
    {
        try {
            // Get tax rules by calling calculate with zero amount
            $result = $this->internalTaxService->calculateTaxForOwner(
                0, // Zero amount to get rules without calculation
                $this->determineOwnerType($context),
                $this->determineOwnerId($context),
                $context
            );

            return array_map(function ($tax) {
                return [
                    'id' => $tax['id'] ?? null,
                    'name' => $tax['name'] ?? 'Tax',
                    'rate' => $tax['rate'] ?? 0,
                    'type' => $tax['type'] ?? 'percentage',
                    'is_inclusive' => $tax['is_inclusive'] ?? false,
                    'priority' => $tax['priority'] ?? 0,
                ];
            }, $result['taxes']);
        } catch (\Exception $e) {
            Log::error('Tax rules retrieval failed', [
                'context' => $context,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Quick tax preview - minimal implementation
     */
    public function previewTax(float $amount, array $context = []): TaxPreview
    {
        $result = $this->calculateTax($amount, $context);

        return new TaxPreview(
            estimatedTax: $result->totalTax,
            taxRateDescription: count($result->taxBreakdown) > 1 ? 'Multiple taxes' : 'Tax applied',
            isTaxExempt: $result->totalTax === 0.0 && !empty($result->taxBreakdown)
        );
    }

    /**
     * Determine owner type from context dynamically
     */
    private function determineOwnerType(array $context): string
    {
        $hierarchy = ['merchant', 'organization', 'franchise', 'branch', 'outlet', 'department', 'warehouse'];

        foreach ($hierarchy as $ownerType) {
            if (!empty($context[$ownerType . '_id'])) {
                return $ownerType;
            }
        }

        return 'system';
    }

    /**
     * Determine owner ID from context
     */
    private function determineOwnerId(array $context): ?string
    {
        $ownerType = $this->determineOwnerType($context);
        return $context[$ownerType . '_id'] ?? null;
    }

    /**
     * Check if any tax in the breakdown is inclusive
     */
    private function hasInclusiveTax(array $taxes): bool
    {
        foreach ($taxes as $tax) {
            if (($tax['is_inclusive'] ?? false)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine calculation type based on applied taxes
     */
    private function determineCalculationType(array $taxes): string
    {
        if (empty($taxes)) {
            return 'no_tax';
        }

        if (count($taxes) > 1) {
            return 'compound';
        }

        return 'simple';
    }
}
