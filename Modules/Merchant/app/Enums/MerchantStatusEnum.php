<?php

namespace Modules\Merchant\Enums;

enum MerchantStatusEnum: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case TEMPORARILY_CLOSED = 'temporarily_closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => __('merchant::enum.merchant_status.open'),
            self::CLOSED => __('merchant::enum.merchant_status.closed'),
            self::TEMPORARILY_CLOSED => __('merchant::enum.merchant_status.temporarily_closed'),
        };
    }
}
