<?php

namespace Modules\Merchant\Enums;

enum DocumentTypeEnum: string
{
    case ID_CARD = 'id_card';
    case SELFIE_WITH_ID_CARD = 'selfie_with_id_card';
    case OTHER = 'other';
    case MERCHANT = 'merchant';
    case BANNER = 'banner';


    public function label(): string
    {
        return match ($this) {
            self::ID_CARD => __('merchant::enum.document_type.id_card'),
            self::SELFIE_WITH_ID_CARD => __('merchant::enum.document_type.selfie_with_id_card'),
            self::OTHER => __('merchant::enum.document_type.other'),
            self::MERCHANT => 'Merchant Document',
            self::BANNER => 'Banner Image',
        };
    }

    public function requiresMeta(): bool
    {
        return match ($this) {
            self::ID_CARD => true,
            self::SELFIE_WITH_ID_CARD => false,
            self::OTHER => false,
            self::MERCHANT => false,
            self::BANNER => false,
        };
    }
}
