<?php

namespace App\Filament\App\Clusters\CRM\Resources;

use App\Filament\App\Clusters\CRM;
use App\Filament\App\Clusters\CRM\Resources\CustomerResource\Pages;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Tag;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = CRM::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Primary Information')
                    ->description('Name, Email & phone Number')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        PhoneInput::make('phone')
                            ->defaultCountry('KE')
                            ->required()
                            ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                            ->focusNumberFormat(PhoneInputNumberType::INTERNATIONAL),
                    ])->columns(3),
                Section::make('Tertiary Information')
                    ->description('Description, Tags & Lead')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('tags')
                            ->relationship('tags', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('company_id', Filament::getTenant()->id))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm(Tag::getForm())
                            ->createOptionModalHeading('Create Tag')
                            ->createOptionUsing(function (array $data): int {
                                $data['company_id'] = Filament::getTenant()->id;

                                return Tag::create($data)->getKey();
                            }),
                        Select::make('lead_id')
                            ->relationship('lead', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('company_id', Filament::getTenant()->id))
                            ->searchable()
                            ->preload()
                            ->createOptionModalHeading('Create Lead')
                            ->createOptionForm(Lead::getForm())
                            ->createOptionUsing(function (array $data): int {
                                $data['company_id'] = Filament::getTenant()->id;

                                return Lead::create($data)->getKey();
                            }),
                    ])->columns(2),
                Forms\Components\Section::make('Documents')
                    ->visibleOn('edit')
                    ->schema([
                        Forms\Components\Repeater::make('documents')
                            ->relationship('documents')
                            ->hiddenLabel()
                            ->reorderable(false)
                            ->addActionLabel('Add Document')
                            ->schema([
                                Forms\Components\FileUpload::make('file_path')
                                    ->required(),
                                Forms\Components\Textarea::make('comments')
                                    ->required(),
                            ])
                            ->columns()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
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
                // Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hidden(fn($record) => $record->trashed()),
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => $record->trashed())
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
                RestoreAction::make()
                    ->color('warning')
            ])
            ->recordUrl(fn($record) => $record->trashed() ? null : Pages\ViewCustomer::getUrl([$record->id]))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            // ...
            Pages\ViewCustomer::class,
            Pages\EditCustomer::class,
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make('Primary Information')
                    ->description('Name, email & phone')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->copyable(),
                        PhoneEntry::make('phone')->displayFormat(PhoneInputNumberType::NATIONAL),
                    ])
                    ->columns(3),
                ComponentsSection::make('Tertiary Information')
                    ->description('Description, Tags & Lead')
                    ->schema([
                        TextEntry::make('description')
                            ->columnSpanFull(),
                        TextEntry::make('lead.name'),
                        TextEntry::make('tags.name')
                    ])->columns(2),
                ComponentsSection::make('Documents')
                    ->hidden(fn($record) => $record->documents->isEmpty())
                    ->schema([
                        RepeatableEntry::make('documents')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('file_path')
                                    ->label('Document')
                                    ->formatStateUsing(fn() => "Download Document")
                                    ->url(fn($record) => Storage::url($record->file_path), true)
                                    ->badge()
                                    ->color(Color::Blue),
                                TextEntry::make('comments'),
                            ])
                            ->columns()
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
