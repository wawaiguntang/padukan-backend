<?php

namespace Modules\Merchant\Enums;

enum DocumentTypeEnum: string
{
    case ID_CARD = 'id_card';
    case SELFIE_WITH_KTP = 'selfie_with_ktp';
    case MERCHANT = 'merchant';
    case BANNER = 'banner';


    public function label(): string
    {
        return match ($this) {
            self::ID_CARD => 'ID Card',
            self::SELFIE_WITH_KTP => 'Selfie with KTP',
            self::MERCHANT => 'Merchant Document',
            self::BANNER => 'Banner Image',
        };
    }

    public function requiresMeta(): bool
    {
        return match ($this) {
            self::ID_CARD => true,
        };
    }
}
