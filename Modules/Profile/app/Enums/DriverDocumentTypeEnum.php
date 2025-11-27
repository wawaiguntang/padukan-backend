<?php

namespace Modules\Profile\Enums;

enum DriverDocumentTypeEnum: string
{
    case ID_CARD = 'id_card';
    case SIM = 'sim';
    case STNK = 'stnk';
    case VEHICLE_PHOTO = 'vehicle_photo';

    public function label(): string
    {
        return match($this) {
            self::ID_CARD => 'ID Card',
            self::SIM => 'SIM',
            self::STNK => 'STNK',
            self::VEHICLE_PHOTO => 'Vehicle Photo',
        };
    }

    public function requiresMeta(): bool
    {
        return match($this) {
            self::ID_CARD, self::SIM, self::STNK => true,
            self::VEHICLE_PHOTO => false,
        };
    }
}