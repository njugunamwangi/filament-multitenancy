<?php

namespace App\Filament\App\Clusters\CRM\Resources\TaskResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\TaskResource;
use App\Mail\SendQuote;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\Quote;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Mail;
use LasseRafn\Initials\Initials;
use Wallo\FilamentSelectify\Components\ToggleButton;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            ActionGroup::make([
                Actions\EditAction::make(),
                Action::make('expenses')
                    ->label('Track Expenses')
                    ->modalDescription('Expenses for task #'.$record->id)
                    ->stickyModalFooter()
                    ->stickyModalHeader()
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('danger')
                    ->modalWidth(MaxWidth::SevenExtraLarge)
                    ->modalSubmitActionLabel('Save')
                    ->fillForm(fn ($record): array => [
                        'currency_id' => $record->expense?->currency_id,
                        'accommodation' => $record->expense?->accommodation,
                        'subsistence' => $record->expense?->subsistence,
                        'equipment' => $record->expense?->equipment,
                        'fuel' => $record->expense?->fuel,
                        'labor' => $record->expense?->labor,
                        'material' => $record->expense?->material,
                        'misc' => $record->expense?->misc,
                    ])
                    ->form(Expense::getForm())
                    ->action(function ($record, array $data) {
                        if ($record->expense) {
                            $record->expense()->update([
                                'company_id' => Filament::getTenant()->id,
                                'currency_id' => $data['currency_id'],
                                'accommodation' => $data['accommodation'],
                                'subsistence' => $data['subsistence'],
                                'equipment' => $data['equipment'],
                                'fuel' => $data['fuel'],
                                'labor' => $data['labor'],
                                'material' => $data['material'],
                                'misc' => $data['misc'],
                                'total' => $data['total'],
                            ]);
                        } else {
                            $record->expense()->create([
                                'company_id' => Filament::getTenant()->id,
                                'currency_id' => $data['currency_id'],
                                'accommodation' => $data['accommodation'],
                                'subsistence' => $data['subsistence'],
                                'equipment' => $data['equipment'],
                                'fuel' => $data['fuel'],
                                'labor' => $data['labor'],
                                'material' => $data['material'],
                                'misc' => $data['misc'],
                                'total' => $data['total'],
                            ]);
                        }
                    })
                    ->after(function ($record) {
                        if ($record->expense) {
                            Notification::make()
                                ->title('Expense updated')
                                ->info()
                                ->icon('heroicon-o-check')
                                ->body('Task expenses have been updated successfully')
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Expense created')
                                ->success()
                                ->icon('heroicon-o-check')
                                ->body('Task expenses have been created successfully')
                                ->send();
                        }
                    }),
                Action::make('quote')
                    ->label('Generate Quote')
                    ->hidden(fn ($record) => $record->quote)
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
                        ToggleButton::make('mail')
                            ->default('true'),
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
                                                self::updatedTotals($get, $set);
                                            })
                                            ->deleteAction(
                                                fn (ActionsAction $action) => $action->after(fn (Get $get, Set $set) => self::updatedTotals($get, $set)),
                                            ),
                                    ])->columnSpan(8),
                                Group::make()
                                    ->schema([
                                        TextInput::make('subtotal')
                                            ->readOnly()
                                            ->prefix(fn (Get $get) => Currency::find($get('currency_id'))->abbr ?? 'CUR')
                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                self::updatedTotals($get, $set);
                                            }),
                                        TextInput::make('taxes')
                                            ->suffix('%')
                                            ->numeric()
                                            ->default(20)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::updatedTotals($get, $set);
                                            }),
                                        TextInput::make('total')
                                            ->prefix(fn (Get $get) => Currency::find($get('currency_id'))->abbr ?? 'CUR')
                                            ->readOnly(),
                                    ])->columnSpan(4),
                            ])
                            ->columns(12),
                        RichEditor::make('notes')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function ($record, array $data) {
                        $company = Filament::getTenant();

                        $series = (new Initials)->name($company->name)->length(str_word_count($company->name))->generate();

                        $quote = $record->quote()->create([
                            'task_id' => $record->id,
                            'customer_id' => $record->customer->id,
                            'currency_id' => $data['currency_id'],
                            'company_id' => $company->id,
                            'subtotal' => str_replace(',', '', $data['subtotal']),
                            'taxes' => $data['taxes'],
                            'total' => str_replace(',', '', $data['total']),
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
        ];
    }

    public static function updatedTotals(Get $get, Set $set): void
    {
        $items = collect($get('items'));

        $subtotal = 0;

        foreach ($items as $item) {
            $aggregate = $item['quantity'] * $item['unit_price'];

            $subtotal += $aggregate;
        }

        $set('subtotal', number_format($subtotal));
        $set('total', number_format($subtotal + ($subtotal * ($get('taxes') / 100))));
    }
}
