<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EntityType: string implements HasLabel
{
    case SoleProprietorship = 'sole_proprietorship';
    case GeneralPartnership = 'general_partnership';
    case LimitedPartnership = 'limited_partnership';
    case LimitedLiabilityPartnership = 'limited_liability_partnership';
    case LimitedLiabilityCompany = 'limited_liability_company';
    case Corporation = 'corporation';
    case Nonprofit = 'nonprofit';

    public const DEFAULT = self::LimitedLiabilityCompany->value;

    public function getLabel(): ?string
    {
        $label = match ($this) {
            self::SoleProprietorship => 'Sole Proprietorship',
            self::GeneralPartnership => 'General Partnership',
            self::LimitedPartnership => 'Limited Partnership (LP)',
            self::LimitedLiabilityPartnership => 'Limited Liability Partnership (LLP)',
            self::LimitedLiabilityCompany => 'Limited Liability Company (LLC)',
            self::Corporation => 'Corporation',
            self::Nonprofit => 'Nonprofit',
        };

        return $label;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
