<?php

namespace App\Filament\App\Clusters\Banking\Resources;

use App\Enums\AccountStatus;
use App\Enums\AccountType;
use App\Filament\App\Clusters\Banking;
use App\Filament\App\Clusters\Banking\Resources\AccountResource\Pages;
use App\Models\Account;
use App\Models\Currency;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Wallo\FilamentSelectify\Components\ToggleButton;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = Banking::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('company_id', Filament::getTenant()->id)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Account Information')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->enum(AccountType::class)
                                            ->options(AccountType::class)
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->default(AccountType::DEFAULT),
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(100),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('number')
                                            ->required()
                                            ->maxLength(20),
                                        ToggleButton::make('enabled')
                                            ->label('Default')
                                            ->offColor('danger')
                                            ->onColor('info')
                                            ->offLabel('No')
                                            ->onLabel('Yes')
                                            ->required(),
                                    ]),
                            ]),
                        Section::make('Currency & Status')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('currency_id')
                                            ->relationship('currency', 'abbr')
                                            ->label('Currency')
                                            ->optionsLimit(40)
                                            ->searchable()
                                            ->live()
                                            ->preload()
                                            ->getSearchResultsUsing(fn (string $search): array => Currency::whereAny([
                                                'name', 'abbr', 'symbol', 'code'], 'like', "%{$search}%")->limit(50)->pluck('abbr', 'id')->toArray())
                                            ->getOptionLabelUsing(fn ($value): ?string => Currency::find($value)?->abbr)
                                            ->loadingMessage('Loading currencies...')
                                            ->searchPrompt('Search currencies by their symbol, abbreviation or country')
                                            ->required(),
                                        Forms\Components\Select::make('status')
                                            ->required()
                                            ->enum(AccountStatus::class)
                                            ->options(AccountStatus::class)
                                            ->searchable()
                                            ->preload()
                                            ->default(AccountStatus::DEFAULT),
                                    ]),
                            ]),
                        Tabs::make('Account Specifications')
                            ->tabs([
                                Tabs\Tab::make('Bank Information')
                                    ->icon('heroicon-o-building-office-2')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('bank_name')
                                                    ->maxLength(100),
                                                PhoneInput::make('bank_phone')
                                                    ->defaultCountry('KE')
                                                    ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                                                    ->focusNumberFormat(PhoneInputNumberType::INTERNATIONAL),
                                            ]),
                                        RichEditor::make('bank_address')
                                            ->columnSpanFull(),
                                    ]),
                                Tabs\Tab::make('Additional Information')
                                    ->icon('heroicon-o-adjustments-horizontal')
                                    ->schema([
                                        Forms\Components\TextInput::make('description')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('notes')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('bank_website')
                                            ->prefix('https://')
                                            ->url()
                                            ->maxLength(255),
                                    ]),
                            ]),
                    ])->columnSpan(8),
                Group::make()
                    ->schema([
                        Section::make('International Banking Details')
                            ->schema([
                                Forms\Components\TextInput::make('bic_swift_code')
                                    ->maxLength(11),
                                Forms\Components\TextInput::make('iban')
                                    ->maxLength(34),
                            ]),
                        Section::make('Routing Information')
                            ->schema([
                                Forms\Components\TextInput::make('aba_routing_number')
                                    ->maxLength(9),
                                Forms\Components\TextInput::make('ach_routing_number')
                                    ->maxLength(9),
                            ]),
                    ])->columnSpan(4),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('bank_name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->label('Default')
                    ->boolean(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Action::make('default')
                        ->color('warning')
                        ->visible(fn ($record) => ! $record->enabled)
                        ->icon('heroicon-o-lock-closed')
                        ->modalDescription(fn ($record) => 'Are you sure you want to make '.$record->name.' default?')
                        ->modalSubmitActionLabel('Make Default')
                        ->iconPosition('center')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $company = Filament::getTenant();

                            Account::where('company_id', $company->id)->where('enabled', true)->update(['enabled' => false]);

                            $record->makeDefault();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'view' => Pages\ViewAccount::route('/{record}'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
