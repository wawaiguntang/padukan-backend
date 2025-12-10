<?php

namespace Modules\Product\Repositories\ProductImage;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductImage;

class ProductImageRepository implements IProductImageRepository
{
    public function __construct(
        protected ProductImage $model
    ) {}

    public function findById(string $id): ?ProductImage
    {
        return $this->model->find($id);
    }

    public function getByProductId(string $productId): Collection
    {
        return $this->model->where('product_id', $productId)
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();
    }

    public function create(array $data): ProductImage
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $image = $this->model->find($id);
        return $image ? $image->update($data) : false;
    }

    public function delete(string $id): bool
    {
        $image = $this->model->find($id);
        return $image ? $image->delete() : false;
    }

    public function deleteByProductId(string $productId): bool
    {
        return $this->model->where('product_id', $productId)->delete() > 0;
    }

    public function setPrimary(string $productId, string $imageId): bool
    {
        // Unset primary for all product images
        $this->model->where('product_id', $productId)->update(['is_primary' => false]);

        // Set new primary
        return $this->update($imageId, ['is_primary' => true]);
    }
}
