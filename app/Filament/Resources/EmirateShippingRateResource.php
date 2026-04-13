<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmirateShippingRateResource\Pages;
use App\Models\EmirateShippingRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EmirateShippingRateResource extends Resource
{
    protected static ?string $model = EmirateShippingRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'UAE emirate shipping';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'emirate rate';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(100)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->maxLength(100)
                ->unique(ignoreRecord: true)
                ->helperText('Used in checkout; keep stable once in production.'),
            Forms\Components\TextInput::make('shipping_rate')
                ->label('Delivery charge (below free-shipping threshold)')
                ->numeric()
                ->required()
                ->suffix(config('bookstore.currency_symbol')),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->color('gray'),
                Tables\Columns\TextColumn::make('shipping_rate')
                    ->label('Rate')
                    ->money(config('bookstore.currency_code', 'AED'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmirateShippingRates::route('/'),
            'create' => Pages\CreateEmirateShippingRate::route('/create'),
            'edit'   => Pages\EditEmirateShippingRate::route('/{record}/edit'),
        ];
    }
}
