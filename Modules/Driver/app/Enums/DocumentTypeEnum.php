<?php

namespace Modules\Driver\Enums;

enum DocumentTypeEnum: string
{
    case ID_CARD = 'id_card';
    case SIM = 'sim';
    case STNK = 'stnk';
    case VEHICLE_PHOTO = 'vehicle_photo';
    case SELFIE_WITH_ID_CARD = 'selfie_with_id_card';

    public function label(): string
    {
        return match ($this) {
            self::ID_CARD => 'ID Card',
            self::SIM => 'SIM',
            self::STNK => 'STNK',
            self::VEHICLE_PHOTO => 'Vehicle Photo',
            self::SELFIE_WITH_ID_CARD => 'Selfie with ID Card',
        };
    }

    public function requiresMeta(): bool
    {
        return match ($this) {
            self::ID_CARD, self::SIM, self::STNK, self::SELFIE_WITH_ID_CARD => true,
            self::VEHICLE_PHOTO => false,
        };
    }
}
