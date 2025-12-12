<?php

namespace App\Shared\Tax;

/**
 * Preview object for quick tax estimates
 */
class TaxPreview
{
    public function __construct(
        public float $estimatedTax,
        public string $taxRateDescription,
        public bool $isTaxExempt = false,
        public ?string $exemptionReason = null
    ) {}

    /**
     * Create TaxPreview from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            estimatedTax: $data['estimatedTax'] ?? 0,
            taxRateDescription: $data['taxRateDescription'] ?? '',
            isTaxExempt: $data['isTaxExempt'] ?? false,
            exemptionReason: $data['exemptionReason'] ?? null
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'estimatedTax' => $this->estimatedTax,
            'taxRateDescription' => $this->taxRateDescription,
            'isTaxExempt' => $this->isTaxExempt,
            'exemptionReason' => $this->exemptionReason,
        ];
    }
}
