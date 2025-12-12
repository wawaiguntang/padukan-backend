<?php

namespace Modules\Tax\Cache;

class TaxTtlManager
{
    // Cache for 1 month
    public const TAX_DATA = 2592000;

    public static function taxData(): int
    {
        return self::TAX_DATA;
    }
}
