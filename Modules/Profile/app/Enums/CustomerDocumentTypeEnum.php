<?php

namespace Modules\Profile\Enums;

enum CustomerDocumentTypeEnum: string
{
    case ID_CARD = 'id_card';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::ID_CARD => 'ID Card',
            self::OTHER => 'Other Document',
        };
    }

    public function requiresMeta(): bool
    {
        return match($this) {
            self::ID_CARD => true,
            self::OTHER => false,
        };
    }
}