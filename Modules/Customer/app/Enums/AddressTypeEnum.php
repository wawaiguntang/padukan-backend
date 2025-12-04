<?php

namespace Modules\Customer\Enums;

enum AddressTypeEnum: string
{
    case HOME = 'home';
    case WORK = 'work';
    case BUSINESS = 'business';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::HOME => __('customer::enum.address_type.home'),
            self::WORK => __('customer::enum.address_type.work'),
            self::BUSINESS => __('customer::enum.address_type.business'),
            self::OTHER => __('customer::enum.address_type.other'),
        };
    }
}
