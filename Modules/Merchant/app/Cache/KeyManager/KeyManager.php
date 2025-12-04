<?php

namespace Modules\Merchant\Cache\KeyManager;

class KeyManager implements IKeyManager
{
    public static function getProfileKey(string $userId): string
    {
        return "merchant:profile:user:{$userId}";
    }

    public static function getProfileByIdKey(string $id): string
    {
        return "merchant:profile:id:{$id}";
    }

    public static function getProfileExistsKey(string $userId): string
    {
        return "merchant:profile:exists:{$userId}";
    }

    public static function getProfileMerchantsCountKey(string $profileId): string
    {
        return "merchant:profile:merchants:count:{$profileId}";
    }

    public static function getMerchantByIdKey(string $id): string
    {
        return "merchant:merchant:id:{$id}";
    }

    public static function getMerchantsByProfileIdKey(string $profileId): string
    {
        return "merchant:merchant:profile:{$profileId}";
    }

    public static function getDocumentsByDocumentableKey(string $documentableId, string $documentableType): string
    {
        return "merchant:document:{$documentableType}:{$documentableId}";
    }

    public static function getDocumentByIdKey(string $id): string
    {
        return "merchant:document:id:{$id}";
    }

    public static function getProfilePattern(string $userId): string
    {
        return "merchant:profile:{$userId}:*";
    }

    public static function getMerchantPattern(string $profileId): string
    {
        return "merchant:merchant:{$profileId}:*";
    }

    public static function getDocumentPattern(string $documentableId): string
    {
        return "merchant:document:*:{$documentableId}";
    }

    public static function getAllMerchantPattern(): string
    {
        return "merchant:*";
    }
}
