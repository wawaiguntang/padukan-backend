<?php

namespace Modules\Tax\Repositories\TaxRate;

use Illuminate\Database\Eloquent\Collection;
use Modules\Tax\Models\TaxRate;

interface ITaxRateRepository
{
    public function findAll(): Collection;
    public function findById(string $id): ?TaxRate;
    public function findByGroup(string $taxGroupId): Collection;
    public function findByTax(string $taxId): Collection;
    public function findActiveByGroup(string $taxGroupId): Collection;
    public function create(array $data): TaxRate;

    /**
     * Create multiple tax rates in bulk for a specific group.
     *
     * @param string $taxGroupId Tax group UUID
     * @param array<array{
     *     tax_id: string,
     *     rate: float,
     *     type: string,
     *     is_inclusive: bool,
     *     priority: int,
     *     based_on: string|null,
     *     valid_from: string|null,
     *     valid_until: string|null,
     *     min_price: float|null,
     *     max_price: float|null
     * }> $ratesData Array of tax rate data
     * @return Collection<TaxRate> Collection of created TaxRate models
     */
    public function createBulkForGroup(string $taxGroupId, array $ratesData): Collection;

    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
}
