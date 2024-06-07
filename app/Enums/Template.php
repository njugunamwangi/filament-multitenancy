<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;

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

    public function getLabel(): ?string
    {
        return match($this) {
            self::DEFAULT => 'Default',
            self::Template1 => 'Template 1',
            self::Template2 => 'Template 2',
            self::Template3 => 'Template 3',
            self::Template4 => 'Template 4',
            self::Template5 => 'Template 5',
            self::Template6 => 'Template 6',
            self::Template7 => 'Template 7',
            self::Template8 => 'Template 8',
        };
    }
}
