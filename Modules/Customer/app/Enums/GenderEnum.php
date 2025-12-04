<?php

namespace Modules\Customer\Enums;

enum GenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MALE => __('customer::enum.gender.male'),
            self::FEMALE => __('customer::enum.gender.female'),
            self::OTHER => __('customer::enum.gender.other'),
        };
    }
}
