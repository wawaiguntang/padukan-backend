<?php

namespace Modules\Customer\Enums;

enum DocumentTypeEnum: string
{
    case ID_CARD = 'id_card';
    case SELFIE_WITH_ID_CARD = 'selfie_with_id_card';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::ID_CARD => 'ID Card',
            self::SELFIE_WITH_ID_CARD => 'Selfie with ID Card',
            self::OTHER => 'Other Document',
        };
    }

    public function requiresMeta(): bool
    {
        return match ($this) {
            self::ID_CARD => true,
            self::SELFIE_WITH_ID_CARD => false,
            self::OTHER => false,
        };
    }
}
