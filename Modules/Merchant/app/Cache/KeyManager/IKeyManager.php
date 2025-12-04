<?php

namespace Modules\Merchant\Cache\KeyManager;

interface IKeyManager
{
    // ==========================================
    // PROFILE DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for profile lookup by user ID
     */
    public static function getProfileKey(string $userId): string;

    public static function getProfileByIdKey(string $id): string;

    public static function getProfileExistsKey(string $userId): string;

    public static function getProfileMerchantsCountKey(string $profileId): string;

    public static function getMerchantByIdKey(string $id): string;

    public static function getMerchantsByProfileIdKey(string $profileId): string;

    public static function getDocumentsByDocumentableKey(string $documentableId, string $documentableType): string;

    public static function getDocumentByIdKey(string $id): string;

    public static function getProfilePattern(string $userId): string;

    public static function getMerchantPattern(string $profileId): string;

    public static function getDocumentPattern(string $documentableId): string;

    public static function getMerchantSettingsByMerchantIdKey(string $merchantId): string;

    public static function getAllMerchantPattern(): string;
}
