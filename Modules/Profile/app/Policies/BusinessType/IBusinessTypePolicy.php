<?php

namespace Modules\Profile\Policies\BusinessValidation;

interface IBusinessTypePolicy
{
    /**
     * Validate business type
     */
    public function validateBusinessType(string $businessType): bool;

    /**
     * Get allowed business types
     */
    public function getAllowedTypes(): array;

    /**
     * Check if business type validation is required
     */
    public function isRequired(): bool;

    /**
     * Check if auto validation should be performed
     */
    public function shouldAutoValidate(): bool;
}