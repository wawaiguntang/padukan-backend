<?php

namespace Modules\Product\Repositories\UnitConversion;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\UnitConversion;

interface IUnitConversionRepository
{
    public function findById(string $id): ?UnitConversion;
    public function getConversions(): Collection;
    public function getByUnit(string $unit): Collection;
    public function create(array $data): UnitConversion;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
}
