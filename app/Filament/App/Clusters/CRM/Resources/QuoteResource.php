<?php

namespace App\Filament\App\Clusters\CRM\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\App\Clusters\CRM;
use App\Filament\App\Clusters\CRM\Resources\QuoteResource\Pages;
use App\Mail\SendInvoice;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
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
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Actions\Action as NotificationsActionsAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;
use LasseRafn\Initials\Initials;
use Wallo\FilamentSelectify\Components\ToggleButton;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $cluster = CRM::class;

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('company_id', Filament::getTenant()->id)->count();
    }

    public static function form(Form $form): Form
    {
        $company_id = Filament::getTenant()->id;

        return $form
            ->schema([
                Section::make('User Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('customer_id')
                                    ->relationship('customer', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('company_id', $company_id))
                                    ->required()
                                    ->live(),
                                Forms\Components\Select::make('task_id')
                                    ->visible(fn (Get $get) => $get('customer_id'))
                                    ->live()
                                    ->relationship('task', 'id', modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('company_id', $company_id)->where('customer_id', $get('customer_id'))->whereDoesntHave('quote')),
                            ]),
                        Forms\Components\Select::make('currency_id')
                            ->relationship('currency', 'abbr')
                            ->default(Company::find($company_id)->currency_id)
                            ->searchable()
                            ->preload()
                            ->optionsLimit(100)
                            ->live()
                            ->required(),
                    ]),
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
                                        fn (Action $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
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
                    ->required()
                    ->label('Send Mail to Customer?'),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task.id')
                    ->getStateUsing(fn ($record) => $record->task ? '#'.$record->task->id : '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.abbr')
                    ->description(fn ($record) => $record->currency->name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('taxes')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serial')
                    ->searchable(),
                Tables\Columns\IconColumn::make('mail')
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
            ->recordUrl(function ($record) {
                return Pages\ViewQuote::getUrl([$record]);
            })
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->color('info'),
                    ActionsAction::make('invoice')
                        ->hidden(fn ($record) => $record->invoice)
                        ->color('success')
                        ->modalWidth(MaxWidth::SixExtraLarge)
                        ->icon('heroicon-o-clipboard-document-check')
                        ->modalIcon('heroicon-o-clipboard-document-check')
                        ->modalDescription(fn ($record) => 'Generate invoice for quote '.$record->serial)
                        ->modalSubmitActionLabel('Generate Invoice')
                        ->label('Generate Invoice')
                        ->fillForm(fn ($record): array => [
                            'items' => $record->items,
                            'taxes' => $record->taxes,
                            'notes' => $record->notes,
                        ])
                        ->form([
                            Select::make('status')
                                ->enum(InvoiceStatus::class)
                                ->options(InvoiceStatus::class)
                                ->default(InvoiceStatus::DEFAULT)
                                ->searchable()
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
                                                    fn (Action $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
                                                ),
                                        ])->columnSpan(8),
                                    Group::make()
                                        ->schema([
                                            TextInput::make('subtotal')
                                                ->readOnly()
                                                ->prefix(fn ($record) => $record->currency->abbr)
                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                    self::updateTotals($get, $set);
                                                }),
                                            TextInput::make('taxes')
                                                ->suffix('%')
                                                ->numeric()
                                                ->default(20)
                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                    self::updateTotals($get, $set);
                                                }),
                                            TextInput::make('total')
                                                ->prefix(fn ($record) => $record->currency->abbr)
                                                ->readOnly(),
                                        ])->columnSpan(4),
                                ])
                                ->columns(12),
                            RichEditor::make('notes')
                                ->columnSpanFull()
                                ->required(),
                            ToggleButton::make('mail')
                                ->default(true)
                                ->label('Send Email to Customer?'),
                        ])
                        ->action(function ($record, array $data) {
                            $company = Filament::getTenant();
                            $series = (new Initials)->name($company->name)->length(str_word_count($company->name))->generate();

                            $invoice = $record->invoice()->create([
                                'task_id' => $record->task->id ?? null,
                                'customer_id' => $record->customer->id,
                                'currency_id' => $record->currency->id,
                                'company_id' => $company->id,
                                'status' => $data['status'],
                                'subtotal' => $data['subtotal'],
                                'taxes' => $record->taxes,
                                'total' => $data['total'],
                                'serial_number' => $serial_number = (Invoice::query()->where('company_id', $company->id)->max('serial_number') ?? 0) + 1,
                                'serial' => $series.'-'.str_pad($serial_number, 5, '0', STR_PAD_LEFT),
                                'items' => $record->items,
                                'notes' => $data['notes'],
                                'mail' => $data['mail'],
                            ]);

                            if ($invoice->mail) {

                                $company = Filament::getTenant();

                                $invoice->savePdf();

                                Mail::to($invoice->customer->email)->send(new SendInvoice($invoice));

                                Notification::make()
                                    ->success()
                                    ->icon('heroicon-o-envelope')
                                    ->title('Invoice mailed')
                                    ->body('Invoice for ' . $company->name . ' mailed to '.$invoice->customer->name)
                                    ->actions([
                                        NotificationsActionsAction::make('read')
                                            ->label('Mark as read')
                                            ->markAsRead()
                                    ])
                                    ->sendToDatabase(User::find($company->user_id));
                            }
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ViewEntry::make('quote')
                    ->columnSpanFull()
                    ->viewData([
                        'record' => $infolist->record,
                    ])
                    ->view('infolists.components.quote-view'),
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'view' => Pages\ViewQuote::route('/{record}'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
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
