<?php

namespace Modules\Promotion\Repositories\Promotion;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Promotion\Models\Promotion;

interface IPromotionRepository
{
    /**
     * Create a new promotion.
     *
     * @param array $data The data for creating the promotion.
     *        Contains: ['code' => string, 'name' => string, 'short_description' => string|null,
     *        'terms_conditions' => string|null, 'banner_image' => string|null, 'owner_type' => string,
     *        'owner_id' => string, 'priority' => int|null, 'stackable' => bool|null, 'start_at' => string|null,
     *        'end_at' => string|null, 'status' => string, 'rules_json' => array|null, 'actions_json' => array|null,
     *        'metadata' => array|null]
     * @return Promotion The created promotion.
     */
    public function create(array $data): Promotion;

    /**
     * Update an existing promotion.
     *
     * @param int $id The ID of the promotion to update.
     * @param array $data The data for updating the promotion.
     *        Contains: ['code' => string, 'name' => string, 'short_description' => string|null,
     *        'terms_conditions' => string|null, 'banner_image' => string|null, 'owner_type' => string,
     *        'owner_id' => string, 'priority' => int|null, 'stackable' => bool|null, 'start_at' => string|null,
     *        'end_at' => string|null, 'status' => string, 'rules_json' => array|null, 'actions_json' => array|null,
     *        'metadata' => array|null]
     * @param array|null $owner The owner filter. ['owner_type' => 'merchant', 'owner_id' => 123]
     * @return Promotion The updated promotion.
     */
    public function update(int $id, array $data, ?array $owner = null): Promotion;

    /**
     * Delete a promotion.
     *
     * @param int $id The ID of the promotion to delete.
     * @param array|null $owner The owner filter. ['owner_type' => string, 'owner_id' => string]
     * @return bool True if deletion is successful, false otherwise.
     */
    public function delete(int $id, ?array $owner = null): bool;

    /**
     * Find a promotion by its ID.
     *
     * @param int $id The ID of the promotion to find.
     * @param array|null $owner The owner filter. ['owner_type' => string, 'owner_id' => string]
     * @return Promotion|null The found promotion or null if not found.
     */
    public function find(int $id, ?array $owner = null): ?Promotion;

    /**
     * Find a promotion by its ID or fail.
     *
     * @param int $id The ID of the promotion to find.
     * @param array|null $owner The owner filter. ['owner_type' => string, 'owner_id' => string]
     * @return Promotion The found promotion.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id, ?array $owner = null): Promotion;

    /**
     * Find a promotion by its code.
     *
     * @param string $code The code of the promotion.
     * @return Promotion|null The found promotion or null if not found.
     */
    public function findByCode(string $code): ?Promotion;

    /**
     * Get all promotions.
     *
     * @param array $filters The filters to apply.
     *        Contains: ['status' => string|null, 'owner_type' => string|null, 'owner_id' => string|null,
     *        'start_at' => string|null, 'end_at' => string|null]
     * @param array|null $owner The owner filter. ['owner_type' => string, 'owner_id' => string]
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\Promotion\Models\Promotion> A collection of promotions.
     */
    public function getAll(array $filters = [], ?array $owner = null): Collection;

    /**
     * Get paginated promotions.
     *
     * @param int $perPage The number of items per page.
     * @param array $filters The filters to apply.
     *        Contains: ['status' => string|null, 'owner_type' => string|null, 'owner_id' => string|null,
     *        'start_at' => string|null, 'end_at' => string|null]
     * @param array|null $owner The owner filter. ['owner_type' => string, 'owner_id' => string]
     * @return LengthAwarePaginator A paginator instance.
     */
    public function getPaginated(int $perPage = 15, array $filters = [], ?array $owner = null): LengthAwarePaginator;

    /**
     * Get active promotions.
     *
     * @param array|null $owner The owner filter. ['owner_type' => string, 'owner_id' => string]
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\Promotion\Models\Promotion> A collection of active promotions.
     */
    public function getActivePromotions(?array $owner = null): Collection;

    /**
     * Get promotions by owner.
     *
     * @param string $ownerType The type of the owner.
     * @param string $ownerId The ID of the owner.
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\Promotion\Models\Promotion> A collection of promotions by the owner.
     */
    public function getPromotionsByOwner(string $ownerType, string $ownerId): Collection;

    /**
     * Find eligible promotions based on a context.
     *
     * @param array $context The context for eligibility check.
     *        Contains: ['user_id' => string|null, 'items' => array, 'total_amount' => float|null]
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\Promotion\Models\Promotion> A collection of eligible promotions.
     */
    public function findEligiblePromotions(array $context): Collection;

    /**
     * Update the status of a promotion.
     *
     * @param int $id The ID of the promotion.
     * @param string $status The new status.
     * @return bool True if update is successful, false otherwise.
     */
    public function updateStatus(int $id, string $status): bool;

    /**
     * Bulk update the status of promotions.
     *
     * @param array $ids An array of promotion IDs.
     * @param string $status The new status.
     * @return bool True if update is successful, false otherwise.
     */
    public function bulkUpdateStatus(array $ids, string $status): bool;

    /**
     * Flush the cache for a promotion.
     *
     * @param int $id The ID of the promotion.
     * @return void
     */
    public function flushCacheForPromotion(int $id): void;
}
