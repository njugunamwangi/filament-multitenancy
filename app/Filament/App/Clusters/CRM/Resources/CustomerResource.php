<?php

namespace App\Filament\App\Clusters\CRM\Resources;

use App\Filament\App\Clusters\CRM;
use App\Filament\App\Clusters\CRM\Resources\CustomerResource\Pages;
use App\Models\CompanyLead;
use App\Models\Customer;
use App\Models\Tag;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = CRM::class;

    public static function form(Form $form): Form
    {
        $leads = DB::table('company_leads')
            ->where('company_id', '=', Filament::getTenant()->id)
            ->join('leads', 'company_leads.lead_id', '=', 'leads.id')
            ->get();

        return $form
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
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('lead_id')
                    ->options($leads->pluck('name', 'lead_id'))
                    ->label('Lead')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('tags')
                // ->options(Tag::where('company_id', Filament::getTenant()->id)->pluck('name', 'id'))
                    ->relationship('tags', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('company_id', Filament::getTenant()->id))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->createOptionForm(Tag::getForm())
                    ->createOptionModalHeading('Create Tag')
                    ->createOptionUsing(function (array $data): int {
                        $data['company_id'] = Filament::getTenant()->id;

                        return Tag::create($data)->getKey();
                    })
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
                TextColumn::make('lead.name'),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->slideOver(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
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
