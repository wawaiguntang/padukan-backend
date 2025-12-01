<?php

namespace Modules\Driver\Repositories\Profile;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Driver\Enums\GenderEnum;
use Modules\Driver\Enums\VehicleTypeEnum;
use Modules\Driver\Models\Profile;
use Modules\Driver\Cache\KeyManager\IKeyManager;
use App\Enums\ServiceTypeEnum;

/**
 * Profile Repository Implementation
 *
 * This class handles all profile-related database operations
 * for the driver module with caching support.
 */
class ProfileRepository implements IProfileRepository
{
    /**
     * The Profile model instance
     *
     * @var Profile
     */
    protected Profile $model;

    /**
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * The cache key manager instance
     *
     * @var IKeyManager
     */
    protected IKeyManager $cacheKeyManager;

    /**
     * Cache TTL in seconds (15 minutes - reasonable for profile data)
     *
     * @var int
     */
    protected int $cacheTtl = 900;

    /**
     * Constructor
     *
     * @param Profile $model The Profile model instance
     * @param Cache $cache The cache repository instance
     * @param IKeyManager $cacheKeyManager The cache key manager instance
     */
    public function __construct(Profile $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    /**
     * {@inheritDoc}
     */
    public function findByUserId(string $userId): ?Profile
    {
        $cacheKey = $this->cacheKeyManager::profileByUserId($userId);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($userId) {
            return $this->model->where('user_id', $userId)->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Profile
    {
        $cacheKey = $this->cacheKeyManager::profileById($id);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Profile
    {
        $profile = $this->model->create($data);

        // Cache invalidation is handled by ProfileObserver

        return $profile;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $profile = $this->model->find($id);

        if (!$profile) {
            return false;
        }

        $result = $profile->update($data);

        // Cache invalidation is handled by ProfileObserver

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $profile = $this->model->find($id); // Don't use cached version for deletes

        if (!$profile) {
            return false;
        }

        $result = $profile->delete();

        // Cache invalidation is handled by ProfileObserver

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function existsByUserId(string $userId): bool
    {
        return $this->model->where('user_id', $userId)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function updateGender(string $id, GenderEnum $gender): bool
    {
        return $this->update($id, ['gender' => $gender]);
    }

    /**
     * {@inheritDoc}
     */
    public function updateVerificationStatus(string $id, bool $isVerified, ?string $verificationStatus = null): bool
    {
        $data = ['is_verified' => $isVerified];

        if ($verificationStatus) {
            $data['verification_status'] = $verificationStatus;
        }

        return $this->update($id, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function updateVerifiedServices(string $id, array $verifiedServices): bool
    {
        return $this->update($id, ['verified_services' => $verifiedServices]);
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableServices(string $id): array
    {
        $profile = $this->findById($id);

        if (!$profile) {
            return [];
        }

        return $profile->getAvailableServices();
    }

    /**
     * {@inheritDoc}
     */
    public function canProvideService(string $id, ServiceTypeEnum $service): bool
    {
        $availableServices = $this->getAvailableServices($id);

        return in_array($service->value, $availableServices);
    }
}
