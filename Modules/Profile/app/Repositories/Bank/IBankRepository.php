<?php

namespace Modules\Profile\Repositories\Bank;

use Modules\Profile\Models\Bank;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Bank Repository
 */
interface IBankRepository
{
    /**
     * Find bank by ID
     */
    public function findById(string $id): ?Bank;

    /**
     * Find bank by code
     */
    public function findByCode(string $code): ?Bank;

    /**
     * Get all active banks
     */
    public function getActiveBanks(): Collection;

    /**
     * Create new bank
     */
    public function create(array $data): Bank;

    /**
     * Update bank
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete bank
     */
    public function delete(string $id): bool;
}