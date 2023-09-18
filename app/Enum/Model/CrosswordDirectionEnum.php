<?php

namespace App\Enum\Model;

enum CrosswordDirectionEnum: string
{
    case Across = 'across';
    case Down = 'down';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
