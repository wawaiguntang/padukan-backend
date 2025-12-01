<?php

namespace Modules\Customer\Enums;

enum DocumentTypeEnum: string
{
    case ID_CARD = 'id_card';
    case SELFIE_WITH_KTP = 'selfie_with_ktp';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::ID_CARD => 'ID Card',
            self::SELFIE_WITH_KTP => 'Selfie with KTP',
            self::OTHER => 'Other Document',
        };
    }

    public function requiresMeta(): bool
    {
        return match ($this) {
            self::ID_CARD => true,
            self::SELFIE_WITH_KTP => false,
            self::OTHER => false,
        };
    }
}
