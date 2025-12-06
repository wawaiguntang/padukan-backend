<?php

namespace Modules\Product\Repositories\AttributeCustom;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\AttributeCustom;

interface IAttributeCustomRepository
{
    public function findById(string $id): ?AttributeCustom;
    public function getByMerchantId(string $merchantId): Collection;
    public function create(array $data): AttributeCustom;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function existsForMerchant(string $merchantId, string $key, ?string $excludeId = null): bool;
}
