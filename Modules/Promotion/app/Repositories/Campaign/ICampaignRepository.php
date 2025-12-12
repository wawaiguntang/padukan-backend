<?php

namespace Modules\Promotion\Repositories\Campaign;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Promotion\Models\Campaign;

interface ICampaignRepository
{
    /**
     * Create a new campaign.
     *
     * @param array $data The data for creating the campaign.
     *        Contains: ['name' => string, 'description' => string, 'banner_image' => string|null,
     *        'start_at' => string|null, 'end_at' => string|null, 'status' => string, 'metadata' => array|null]
     * @return Campaign The created campaign.
     */
    public function create(array $data): Campaign;

    /**
     * Update an existing campaign.
     *
     * @param int $id The ID of the campaign to update.
     * @param array $data The data for updating the campaign.
     *        Contains: ['name' => string, 'description' => string, 'banner_image' => string|null,
     *        'start_at' => string|null, 'end_at' => string|null, 'status' => string, 'metadata' => array|null]
     * @return Campaign The updated campaign.
     */
    public function update(int $id, array $data): Campaign;

    /**
     * Delete a campaign.
     *
     * @param int $id The ID of the campaign to delete.
     * @return bool True if deletion is successful, false otherwise.
     */
    public function delete(int $id): bool;

    /**
     * Find a campaign by its ID.
     *
     * @param int $id The ID of the campaign to find.
     * @return Campaign|null The found campaign or null if not found.
     */
    public function find(int $id): ?Campaign;

    /**
     * Find a campaign by its ID or fail.
     *
     * @param int $id The ID of the campaign to find.
     * @return Campaign The found campaign.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Campaign;

    /**
     * Get all campaigns.
     *
     * @param array $filters The filters to apply.
     *        Contains: ['status' => string|null, 'start_at' => string|null, 'end_at' => string|null]
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\Promotion\Models\Campaign> A collection of campaigns.
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Get paginated campaigns.
     *
     * @param int $perPage The number of items per page.
     * @param array $filters The filters to apply.
     *        Contains: ['status' => string|null, 'start_at' => string|null, 'end_at' => string|null]
     * @return LengthAwarePaginator A paginator instance.
     */
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Get active campaigns.
     *
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\Promotion\Models\Campaign> A collection of active campaigns.
     */
    public function getActiveCampaigns(): Collection;

    /**
     * Add a promotion to a campaign.
     *
     * @param int $campaignId The ID of the campaign.
     * @param int $promotionId The ID of the promotion.
     * @param array|null $owner The owner filter for the promotion.
     *        Contains: ['owner_type' => string, 'owner_id' => string]
     * @return bool True if successful, false otherwise.
     */
    public function addPromotion(int $campaignId, int $promotionId, ?array $owner = null): bool;

    /**
     * Remove a promotion from a campaign.
     *
     * @param int $campaignId The ID of the campaign.
     * @param int $promotionId The ID of the promotion.
     * @param array|null $owner The owner filter for the promotion.
     *        Contains: ['owner_type' => string, 'owner_id' => string]
     * @return bool True if successful, false otherwise.
     */
    public function removePromotion(int $campaignId, int $promotionId, ?array $owner = null): bool;

    /**
     * Sync promotions for a campaign.
     *
     * @param int $campaignId The ID of the campaign.
     * @param array $promotionIds An array of promotion IDs.
     * @param array|null $owner The owner filter for the promotions.
     *        Contains: ['owner_type' => string, 'owner_id' => string]
     * @return void
     */
    public function syncPromotions(int $campaignId, array $promotionIds, ?array $owner = null): void;

    /**
     * Get all promotions for a campaign.
     *
     * @param int $campaignId The ID of the campaign.
     * @return \Illuminate\Database\Eloquent\Collection<\Modules\Promotion\Models\Promotion> A collection of promotions.
     */
    public function getPromotions(int $campaignId): Collection;

    /**
     * Flush the cache for a campaign.
     *
     * @param int $id The ID of the campaign.
     * @return void
     */
    public function flushCacheForCampaign(int $id): void;
}
