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
            self::ID_CARD => __('customer::enum.document_type.id_card'),
            self::SELFIE_WITH_ID_CARD => __('customer::enum.document_type.selfie_with_id_card'),
            self::OTHER => __('customer::enum.document_type.other'),
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
