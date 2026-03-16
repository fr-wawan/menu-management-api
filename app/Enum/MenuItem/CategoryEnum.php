<?php

declare(strict_types=1);

namespace App\Enum\MenuItem;

enum CategoryEnum: string
{
    case APPETIZER = 'appetizer';
    case MAIN_COURSE = 'main_course';
    case DESSERT = 'dessert';
    case DRINK = 'drink';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
