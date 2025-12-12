<?php

namespace Modules\Promotion\Repositories\Promotion;

use Modules\Promotion\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PromotionRepository implements IPromotionRepository
{
    public function create(array $data): Promotion
    {
        return Promotion::create($data);
    }

    public function update(int $id, array $data, ?array $owner = null): Promotion
    {
        $promotion = $this->findOrFail($id, $owner);
        $promotion->update($data);
        return $promotion;
    }

    public function delete(int $id, ?array $owner = null): bool
    {
        $promotion = $this->findOrFail($id, $owner);
        return $promotion->delete();
    }

    public function find(int $id, ?array $owner = null): ?Promotion
    {
        $query = Promotion::query();

        if ($owner) {
            $query->where('owner_type', $owner['owner_type'])
                ->where('owner_id', $owner['owner_id']);
        }

        return $query->find($id);
    }

    public function findOrFail(int $id, ?array $owner = null): Promotion
    {
        $promotion = $this->find($id, $owner);

        if (!$promotion) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Promotion with ID {$id} not found.");
        }

        return $promotion;
    }

    public function findByCode(string $code): ?Promotion
    {
        return Promotion::where('code', $code)->first();
    }

    public function getAll(array $filters = [], ?array $owner = null): Collection
    {
        // Implementation for filtering can be added here
        $query = Promotion::query();

        if ($owner) {
            $query->where('owner_type', $owner['owner_type'])
                ->where('owner_id', $owner['owner_id']);
        }

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['owner_type'])) {
            $query->where('owner_type', $filters['owner_type']);
        }

        if (isset($filters['owner_id'])) {
            $query->where('owner_id', $filters['owner_id']);
        }

        return $query->get();
    }

    public function getPaginated(int $perPage = 15, array $filters = [], ?array $owner = null): LengthAwarePaginator
    {
        $query = Promotion::query();

        if ($owner) {
            $query->where('owner_type', $owner['owner_type'])
                ->where('owner_id', $owner['owner_id']);
        }

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['owner_type'])) {
            $query->where('owner_type', $filters['owner_type']);
        }

        if (isset($filters['owner_id'])) {
            $query->where('owner_id', $filters['owner_id']);
        }

        return $query->paginate($perPage);
    }

    public function getActivePromotions(?array $owner = null): Collection
    {
        $query = Promotion::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            });

        if ($owner) {
            $query->where('owner_type', $owner['owner_type'])
                ->where('owner_id', $owner['owner_id']);
        }

        return $query->get();
    }

    public function getPromotionsByOwner(string $ownerType, string $ownerId): Collection
    {
        return Promotion::where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->get();
    }

    public function findEligiblePromotions(array $context): Collection
    {
        // Basic implementation - can be enhanced based on business rules
        $query = Promotion::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            });

        // Add more eligibility logic based on context
        // This is a placeholder for more complex business rules

        return $query->get();
    }

    public function updateStatus(int $id, string $status): bool
    {
        return Promotion::where('id', $id)->update(['status' => $status]);
    }

    public function bulkUpdateStatus(array $ids, string $status): bool
    {
        return Promotion::whereIn('id', $ids)->update(['status' => $status]);
    }

    public function flushCacheForPromotion(int $id): void
    {
        // Cache management should be handled at service layer
    }
}
