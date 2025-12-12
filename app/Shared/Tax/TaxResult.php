<?php

namespace App\Shared\Tax;

/**
 * Result object for tax calculations
 */
class TaxResult
{
    public function __construct(
        public float $originalAmount,
        public float $totalTax,
        public float $finalAmount,
        public array $taxBreakdown,
        public bool $isInclusive = false,
        public array $appliedRules = [],
        public string $calculationType = 'standard'
    ) {}

    /**
     * Create TaxResult from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            originalAmount: $data['originalAmount'] ?? 0,
            totalTax: $data['totalTax'] ?? 0,
            finalAmount: $data['finalAmount'] ?? 0,
            taxBreakdown: $data['taxBreakdown'] ?? [],
            isInclusive: $data['isInclusive'] ?? false,
            appliedRules: $data['appliedRules'] ?? [],
            calculationType: $data['calculationType'] ?? 'standard'
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'originalAmount' => $this->originalAmount,
            'totalTax' => $this->totalTax,
            'finalAmount' => $this->finalAmount,
            'taxBreakdown' => $this->taxBreakdown,
            'isInclusive' => $this->isInclusive,
            'appliedRules' => $this->appliedRules,
            'calculationType' => $this->calculationType,
        ];
    }
}
