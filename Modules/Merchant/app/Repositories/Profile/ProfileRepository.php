<?php

namespace Modules\Merchant\Repositories\Profile;

use Illuminate\Support\Facades\Cache;
use Modules\Merchant\Models\Profile;
use Modules\Merchant\Cache\KeyManager\IKeyManager;

/**
 * Profile Repository Implementation
 *
 * Handles profile data operations with caching
 */
class ProfileRepository implements IProfileRepository
{
    private IKeyManager $keyManager;
    private int $cacheTtl = 900; // 15 minutes

    public function __construct(IKeyManager $keyManager)
    {
        $this->keyManager = $keyManager;
    }

    /**
     * Create a new profile
     */
    public function create(array $data): Profile
    {
        return Profile::create($data);
    }

    /**
     * Find profile by user ID
     */
    public function findByUserId(string $userId): ?Profile
    {
        $cacheKey = $this->keyManager->getProfileKey($userId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($userId) {
            return Profile::where('user_id', $userId)->first();
        });
    }

    /**
     * Find profile by ID
     */
    public function findById(string $profileId): ?Profile
    {
        $cacheKey = $this->keyManager->getProfileByIdKey($profileId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($profileId) {
            return Profile::find($profileId);
        });
    }

    /**
     * Update profile by user ID
     */
    public function updateByUserId(string $userId, array $data): bool
    {
        $profile = Profile::where('user_id', $userId)->first();

        if (!$profile) {
            return false;
        }

        $updated = $profile->update($data);

        if ($updated) {
            // Clear cache
            $cacheKey = $this->keyManager->getProfileKey($userId);
            Cache::forget($cacheKey);

            $cacheKeyById = $this->keyManager->getProfileByIdKey($profile->id);
            Cache::forget($cacheKeyById);
        }

        return $updated;
    }

    /**
     * Update profile by ID
     */
    public function update(string $profileId, array $data): bool
    {
        $profile = Profile::find($profileId);

        if (!$profile) {
            return false;
        }

        $updated = $profile->update($data);

        if ($updated) {
            // Clear cache
            $cacheKey = $this->keyManager->getProfileKey($profile->user_id);
            Cache::forget($cacheKey);

            $cacheKeyById = $this->keyManager->getProfileByIdKey($profileId);
            Cache::forget($cacheKeyById);
        }

        return $updated;
    }

    /**
     * Delete profile by user ID
     */
    public function deleteByUserId(string $userId): bool
    {
        $profile = Profile::where('user_id', $userId)->first();

        if (!$profile) {
            return false;
        }

        $deleted = $profile->delete();

        if ($deleted) {
            // Clear cache
            $cacheKey = $this->keyManager->getProfileKey($userId);
            Cache::forget($cacheKey);

            $cacheKeyById = $this->keyManager->getProfileByIdKey($profile->id);
            Cache::forget($cacheKeyById);
        }

        return $deleted;
    }

    /**
     * Delete profile by ID
     */
    public function delete(string $profileId): bool
    {
        $profile = Profile::find($profileId);

        if (!$profile) {
            return false;
        }

        $deleted = $profile->delete();

        if ($deleted) {
            // Clear cache
            $cacheKey = $this->keyManager->getProfileKey($profile->user_id);
            Cache::forget($cacheKey);

            $cacheKeyById = $this->keyManager->getProfileByIdKey($profileId);
            Cache::forget($cacheKeyById);
        }

        return $deleted;
    }

    /**
     * Update profile gender
     */
    public function updateGender(string $profileId, \Modules\Merchant\Enums\GenderEnum $gender): bool
    {
        return $this->update($profileId, ['gender' => $gender]);
    }

    /**
     * Check if user has a profile
     */
    public function existsByUserId(string $userId): bool
    {
        $cacheKey = $this->keyManager->getProfileExistsKey($userId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($userId) {
            return Profile::where('user_id', $userId)->exists();
        });
    }

    /**
     * Get merchants count for a profile
     */
    public function countMerchantsByProfileId(string $profileId): int
    {
        $cacheKey = $this->keyManager->getProfileMerchantsCountKey($profileId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($profileId) {
            return Profile::find($profileId)?->merchants()->count() ?? 0;
        });
    }
}
