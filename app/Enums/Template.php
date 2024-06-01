<?php

namespace App\Enums;

enum Template: string
{
    case Default = 'default';
    case Template1 = 'template_1';
    case Template2 = 'template_2';
    case Template3 = 'template_3';
    case Template4 = 'template_4';
    case Template5 = 'template_5';
    case Template6 = 'template_6';
    case Template7 = 'template_7';
    case Template8 = 'template_8';

    public const DEFAULT = self::Default->value;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
