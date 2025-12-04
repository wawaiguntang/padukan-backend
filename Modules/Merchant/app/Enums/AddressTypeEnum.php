<?php

namespace Modules\Merchant\Enums;

enum AddressTypeEnum: string
{
    case HOME = 'home';
    case WORK = 'work';
    case BUSINESS = 'business';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::HOME => __('merchant::enum.address_type.home'),
            self::WORK => __('merchant::enum.address_type.work'),
            self::BUSINESS => __('merchant::enum.address_type.business'),
            self::OTHER => __('merchant::enum.address_type.other'),
        };
    }
}