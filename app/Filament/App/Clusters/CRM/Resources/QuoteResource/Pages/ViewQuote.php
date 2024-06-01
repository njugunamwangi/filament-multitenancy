<?php

namespace App\Filament\App\Clusters\CRM\Resources\QuoteResource\Pages;

use App\Enums\InvoiceStatus;
use App\Enums\Template;
use App\Filament\App\Clusters\CRM\Resources\QuoteResource;
use App\Mail\SendInvoice;
use App\Models\Currency;
use App\Models\Invoice;
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
use Illuminate\Support\Facades\Mail;
use LasseRafn\Initials\Initials;
use Wallo\FilamentSelectify\Components\ToggleButton;

class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Actions\EditAction::make(),
                Action::make('invoice')
                    ->hidden(fn ($record) => $record->invoice)
                    ->color('success')
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
                            'subtotal' => str_replace(',', '', $data['subtotal']),
                            'taxes' => $record->taxes,
                            'total' => str_replace(',', '', $data['total']),
                            'serial_number' => $serial_number = (Invoice::query()->where('company_id', $company->id)->max('serial_number') ?? 0) + 1,
                            'serial' => $series.'-'.str_pad($serial_number, 5, '0', STR_PAD_LEFT),
                            'items' => $record->items,
                            'notes' => $data['notes'],
                            'mail' => $data['mail'],
                        ]);

                        if ($invoice->mail) {

                            $invoice->savePdf();

                            Mail::to($invoice->customer->email)->send(new SendInvoice($invoice));

                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-bolt')
                                ->title('Invoice mailed')
                                ->body('Invoice mailed to '.$invoice->customer->name)
                                ->send();
                        }
                    }),
                Action::make('template')
                    ->label('Change Template')
                    ->color('warning')
                    ->icon('heroicon-o-document-check')
                    ->fillForm(fn($record) => [
                        'template' => $record->template
                    ])
                    ->form([
                        Select::make('template')
                            ->options(Template::class)
                            ->enum(Template::class)
                            ->searchable()
                            ->preload()
                    ])
                    ->action(function($record, array $data) {
                        $record->template = $data['template'];

                        $record->save();
                    })
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
