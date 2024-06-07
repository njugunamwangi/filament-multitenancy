<?php

namespace App\Filament\App\Clusters\CRM\Resources;

use App\Filament\App\Clusters\CRM;
use App\Filament\App\Clusters\CRM\Resources\CustomerResource\Pages;
use App\Mail\SendQuote;
use App\Models\Company;
use App\Models\Company\Lead as CompanyLead;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\Tag;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as ComponentsActionsAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions\Action as ActionsAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use LasseRafn\Initials\Initials;
use Wallo\FilamentSelectify\Components\ToggleButton;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = CRM::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('company_id', Filament::getTenant()->id)->count();
    }

    public static function form(Form $form): Form
    {
        $company = Filament::getTenant();

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
                            // ->relationship('lead', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('company_id', Filament::getTenant()->id))
                            ->options(function() use ($company) {
                                $leads = CompanyLead::where('company_id', $company->id)->pluck('lead_id')->toArray();

                                return Lead::whereIn('id', $leads)->pluck('name', 'id');
                            })
                            ->label('Lead')
                            ->searchable()
                            ->preload(),
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
                            ->columns(),
                    ]),
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
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    Tables\Actions\EditAction::make()
                        ->hidden(fn ($record) => $record->trashed())
                        ->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                    RestoreAction::make()
                        ->color('warning'),
                    Action::make('task')
                        ->label('Add Task')
                        ->icon('heroicon-o-calendar-days')
                        ->color('success')
                        ->modalSubmitActionLabel('Add Task')
                        ->form([
                            DatePicker::make('due_date'),
                            Textarea::make('description')
                                ->required()
                                ->columnSpanFull(),
                            Grid::make(2)
                                ->schema([
                                    ToggleButton::make('requires_equipment')
                                        ->live(),
                                    ToggleButton::make('is_completed')
                                        ->label('Completed?'),
                                ]),
                            Select::make('equipment')
                                ->live()
                                ->visible(fn (Get $get) => $get('requires_equipment'))
                                ->requiredWith('requires_equipment')
                                ->multiple()
                                ->options(fn () => Equipment::query()->where('company_id', Filament::getTenant()->id)->get()->pluck('registration', 'id'))
                                ->preload(),
                        ])
                        ->action(function (array $data, $record) {
                            $data['company_id'] = Filament::getTenant()->id;

                            $task = $record->tasks()->create([
                                'company_id' => $data['company_id'],
                                'due_date' => $data['due_date'],
                                'requires_equipment' => $data['requires_equipment'],
                                'is_completed' => $data['is_completed'],
                                'description' => $data['description'],
                            ]);

                            if ($data['requires_equipment'] && ! empty($data['equipment'])) {
                                $task->equipment()->attach($data['equipment']);
                            }

                            Notification::make()
                                ->title('New Task #'.$record->id)
                                ->success()
                                ->icon('heroicon-o-calendar-days')
                                ->send();
                        }),
                    Action::make('quote')
                        ->label('Generate Quote')
                        ->icon('heroicon-o-document-check')
                        ->color('warning')
                        ->modalWidth(MaxWidth::SixExtraLarge)
                        ->modalSubmitActionLabel('Generate Quote')
                        ->form([
                            Select::make('currency_id')
                                ->options(Currency::all()->pluck('abbr', 'id'))
                                ->label('Currency')
                                ->default(Company::find(Filament::getTenant()->id)->currency_id)
                                ->searchable()
                                ->preload()
                                ->optionsLimit(100)
                                ->live()
                                ->required(),
                            Group::make()
                                ->columnSpanFull()
                                ->schema([
                                    Group::make()
                                        ->schema([
                                            Repeater::make('items')
                                                ->schema([
                                                    Textarea::make('description')
                                                        ->required(),
                                                    TextInput::make('quantity')
                                                        ->default(1)
                                                        ->required()
                                                        ->numeric(),
                                                    TextInput::make('unit_price')
                                                        ->default(10000)
                                                        ->required()
                                                        ->live()
                                                        ->numeric(),
                                                ])
                                                ->addActionLabel('Add Item')
                                                ->columns(3)
                                                ->live()
                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                    self::updateTotals($get, $set);
                                                })
                                                ->deleteAction(
                                                    fn (ComponentsActionsAction $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
                                                ),
                                        ])->columnSpan(8),
                                    Group::make()
                                        ->schema([
                                            Forms\Components\TextInput::make('subtotal')
                                                ->numeric()
                                                ->readOnly()
                                                ->live()
                                                ->prefix(fn (Get $get) => Currency::where('id', $get('currency_id'))->first()->abbr ?? 'CUR')
                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                    self::updateTotals($get, $set);
                                                }),
                                            Forms\Components\TextInput::make('taxes')
                                                ->suffix('%')
                                                ->required()
                                                ->numeric()
                                                ->default(16)
                                                ->live(true)
                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                    self::updateTotals($get, $set);
                                                }),
                                            Forms\Components\TextInput::make('total')
                                                ->numeric()
                                                ->readOnly()
                                                ->prefix(fn (Get $get) => Currency::where('id', $get('currency_id'))->first()->abbr ?? 'CUR'),
                                        ])->columnSpan(4),
                                ])
                                ->columns(12),
                            RichEditor::make('notes')
                                ->required()
                                ->columnSpanFull(),
                            ToggleButton::make('mail')
                                ->label('Send Email?')
                                ->default('true'),
                        ])
                        ->action(function ($record, array $data) {
                            $company = Filament::getTenant();

                            $series = (new Initials)->name($company->name)->length(str_word_count($company->name))->generate();

                            $quote = $record->quotes()->create([
                                'currency_id' => $data['currency_id'],
                                'company_id' => $company->id,
                                'subtotal' => $data['subtotal'],
                                'taxes' => $data['taxes'],
                                'total' => $data['total'],
                                'serial_number' => $serial_number = (Quote::query()->where('company_id', $company->id)->max('serial_number') ?? 0) + 1,
                                'serial' => $series.'-'.str_pad($serial_number, 5, '0', STR_PAD_LEFT),
                                'items' => $data['items'],
                                'notes' => $data['notes'],
                                'mail' => $data['mail'],
                            ]);

                            if ($quote->mail) {

                                $quote->savePdf();

                                Mail::to($quote->customer->email)->send(new SendQuote($quote));

                                Notification::make()
                                    ->warning()
                                    ->icon('heroicon-o-bolt')
                                    ->title('Quote mailed')
                                    ->body('Quote mailed to '.$quote->customer->name)
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->recordUrl(fn ($record) => $record->trashed() ? null : Pages\ViewCustomer::getUrl([$record->id]))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $items = collect($get('items'));

        $subtotal = 0;

        foreach ($items as $item) {
            $aggregate = $item['quantity'] * $item['unit_price'];

            $subtotal += $aggregate;
        }

        $currency = Currency::where('id', $get('currency_id'))->first();

        $set('subtotal', number_format($subtotal, $currency->precision ?? 0, '.', ''));
        $set('total', number_format($subtotal + ($subtotal * ($get('taxes') / 100)), $currency->precision ?? 0, '.', ''));
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
                        TextEntry::make('tags.name'),
                    ])->columns(2),
                ComponentsSection::make('Documents')
                    ->hidden(fn ($record) => $record->documents->isEmpty())
                    ->schema([
                        RepeatableEntry::make('documents')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('file_path')
                                    ->label('Document')
                                    ->formatStateUsing(fn () => 'Download Document')
                                    ->url(fn ($record) => Storage::url($record->file_path), true)
                                    ->badge()
                                    ->color(Color::Blue),
                                TextEntry::make('comments'),
                            ])
                            ->columns(),
                    ]),
                ComponentsSection::make('Tasks')
                    ->visible(fn ($record) => $record->tasks()->count() > 0)
                    ->schema([
                        Tabs::make()
                            ->tabs([
                                Tabs\Tab::make('Complete Tasks')
                                    ->hiddenLabel()
                                    ->badge(fn ($record) => $record->completeTasks()->count())
                                    ->schema([
                                        RepeatableEntry::make('completeTasks')
                                            ->schema([
                                                TextEntry::make('task')
                                                    ->getStateUsing(fn ($record) => '#'.$record->id),
                                                TextEntry::make('due_date')
                                                    ->date(),
                                            ])
                                            ->columns(),
                                    ]),
                                Tabs\Tab::make('Incomplete Tasks')
                                    ->hiddenLabel()
                                    ->badge(fn ($record) => $record->incompleteTasks()->count())
                                    ->schema([
                                        RepeatableEntry::make('incompleteTasks')
                                            ->schema([
                                                TextEntry::make('task')
                                                    ->getStateUsing(fn ($record) => '#'.$record->id),
                                                TextEntry::make('due_date')
                                                    ->date()
                                                    ->suffixAction(
                                                        ActionsAction::make('completed')
                                                            ->visible(fn ($record) => ! $record->is_completed)
                                                            ->label('Mark as completed')
                                                            ->requiresConfirmation()
                                                            ->color('success')
                                                            ->button()
                                                            ->icon('heroicon-o-check-badge')
                                                            ->action(function ($record) {
                                                                $record->completed();

                                                                Notification::make()
                                                                    ->title('Task Completed')
                                                                    ->body('You marked task #'.$record->id.' as completed')
                                                                    ->success()
                                                                    ->send();
                                                            })
                                                    ),
                                            ])
                                            ->columns(),
                                    ]),
                            ]),
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
