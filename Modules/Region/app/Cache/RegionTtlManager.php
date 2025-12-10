<?php

namespace Modules\Region\Cache;

class RegionTtlManager
{
    // Cache for 1 month
    public const GEODATA = 2592000;

    public static function geoData(): int
    {
        return self::GEODATA;
    }
}
