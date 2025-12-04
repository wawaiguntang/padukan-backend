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
            self::ID_CARD => __('driver::enum.document_type.id_card'),
            self::SIM => __('driver::enum.document_type.sim'),
            self::STNK => __('driver::enum.document_type.stnk'),
            self::VEHICLE_PHOTO => __('driver::enum.document_type.vehicle_photo'),
            self::SELFIE_WITH_ID_CARD => __('driver::enum.document_type.selfie_with_id_card'),
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
