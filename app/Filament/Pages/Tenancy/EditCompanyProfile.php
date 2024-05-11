<?php

namespace App\Filament\Pages\Tenancy;

use App\Enums\EntityType;
use App\Models\Profile;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class EditCompanyProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Company Profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                CuratorPicker::make('logo_id')
                                    ->label('Choose Logo'),
                            ])->columnSpan(4),
                        Section::make()
                            ->schema([
                                TextInput::make('name'),
                            ])->columnSpan(8)
                    ])->columns(12),
                Group::make()
                    ->schema([
                        Section::make('Communication')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('email')
                                            ->email(),
                                        PhoneInput::make('phone')
                                            ->defaultCountry('KE')
                                            ->required()
                                            ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                                            ->focusNumberFormat(PhoneInputNumberType::INTERNATIONAL),
                                    ])
                                ]),
                        Section::make('Currency & Registration')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('currency_id')
                                            ->relationship('currency', 'abbr')
                                            ->searchable()
                                            ->preload(),
                                        TextInput::make('company_registration'),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('company_kra_pin')
                                            ->label('KRA Pin'),
                                        Select::make('entity_type')
                                            ->enum(EntityType::class)
                                            ->options(EntityType::class)
                                            ->searchable()
                                            ->preload()
                                            ->default(EntityType::DEFAULT)
                                    ])
                            ])
                    ])
            ]);
    }
}
