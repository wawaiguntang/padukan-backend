<?php

namespace Modules\Customer\Observers;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Customer\Cache\KeyManager\IKeyManager;
use Modules\Customer\Models\Address;

/**
 * Address Model Observer
 *
 * Handles cache management for Address model events
 */
class AddressObserver
{
    /**
     * Cache repository instance
     */
    protected Cache $cache;

    /**
     * Cache key manager instance
     */
    protected IKeyManager $keyManager;

    /**
     * Cache TTL in seconds (15 minutes)
     */
    protected int $cacheTtl = 900;

    /**
     * Constructor
     */
    public function __construct(Cache $cache, IKeyManager $keyManager)
    {
        $this->cache = $cache;
        $this->keyManager = $keyManager;
    }

    /**
     * Handle the Address "created" event
     */
    public function created(Address $address): void
    {
        $this->invalidateProfileAddressesCache($address->profile_id);
    }

    /**
     * Handle the Address "updated" event
     */
    public function updated(Address $address): void
    {
        // Get original profile ID before update
        $originalProfileId = $address->getOriginal('profile_id');

        // Invalidate caches
        $this->invalidateAddressCaches($address->id);
        $this->invalidateProfileAddressesCache($originalProfileId);

        // If profile changed, invalidate new profile cache too
        if ($address->profile_id !== $originalProfileId) {
            $this->invalidateProfileAddressesCache($address->profile_id);
        }
    }

    /**
     * Handle the Address "deleted" event
     */
    public function deleted(Address $address): void
    {
        // Invalidate all related caches
        $this->invalidateAddressCaches($address->id);
        $this->invalidateProfileAddressesCache($address->profile_id);
    }

    /**
     * Invalidate profile addresses cache
     */
    protected function invalidateProfileAddressesCache(string $profileId): void
    {
        $this->cache->forget($this->keyManager::addressesByProfileId($profileId));
    }

    /**
     * Invalidate all cache keys related to an address
     */
    protected function invalidateAddressCaches(string $addressId): void
    {
        $this->cache->forget($this->keyManager::addressById($addressId));
    }
}
