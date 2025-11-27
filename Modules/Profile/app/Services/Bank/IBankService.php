<?php

namespace Modules\Profile\Services\Bank;

use Modules\Profile\Models\Bank;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Bank Service
 *
 * Handles bank data management business logic
 */
interface IBankService
{
    /**
     * Get all active banks
     */
    public function getActiveBanks(): Collection;

    /**
     * Find bank by ID
     */
    public function getBankById(string $bankId): ?Bank;

    /**
     * Find bank by code
     */
    public function getBankByCode(string $code): ?Bank;

    /**
     * Create a new bank (admin only)
     */
    public function createBank(array $data): Bank;

    /**
     * Update bank information (admin only)
     */
    public function updateBank(string $bankId, array $data): bool;

    /**
     * Deactivate a bank (admin only)
     */
    public function deactivateBank(string $bankId): bool;

    /**
     * Validate bank code uniqueness
     */
    public function isBankCodeUnique(string $code, ?string $excludeId = null): bool;
}