<?php

namespace Modules\Profile\Enums;

enum MerchantDocumentTypeEnum: string
{
    case ID_CARD = 'id_card';
    case STORE = 'store';

    public function label(): string
    {
        return match($this) {
            self::ID_CARD => 'ID Card',
            self::STORE => 'Store License',
        };
    }

    public function requiresMeta(): bool
    {
        return match($this) {
            self::ID_CARD => true,
            self::STORE => true,
        };
    }
}