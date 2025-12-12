<?php

namespace Modules\Product\Repositories\UnitConversion;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\UnitConversion;

class UnitConversionRepository implements IUnitConversionRepository
{
    protected UnitConversion $model;

    public function __construct(UnitConversion $model)
    {
        $this->model = $model;
    }

    public function findById(string $id): ?UnitConversion
    {
        return $this->model->find($id);
    }

    public function getConversions(): Collection
    {
        return $this->model->orderBy('from_unit')->get();
    }

    public function getByUnit(string $unit): Collection
    {
        return $this->model->where('from_unit', $unit)
            ->orWhere('to_unit', $unit)
            ->orderBy('from_unit')
            ->get();
    }

    public function create(array $data): UnitConversion
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $conversion = $this->model->find($id);
        if (!$conversion) return false;

        return $conversion->update($data);
    }

    public function delete(string $id): bool
    {
        $conversion = $this->model->find($id);
        if (!$conversion) return false;

        return $conversion->delete();
    }
}
