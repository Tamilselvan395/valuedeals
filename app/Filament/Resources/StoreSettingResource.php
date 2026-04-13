<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreSettingResource\Pages;
use App\Models\StoreSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StoreSettingResource extends Resource
{
    protected static ?string $model = StoreSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Store & currency';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'store settings';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Shipping rules')->schema([
                Forms\Components\TextInput::make('free_shipping_threshold')
                    ->label('Free shipping from (order subtotal)')
                    ->numeric()
                    ->required()
                    ->suffix(config('bookstore.currency_symbol'))
                    ->helperText('Orders at or above this merchandise total ship free (before coupon discount).'),
                Forms\Components\TextInput::make('default_shipping_rate')
                    ->label('Default delivery charge (below threshold)')
                    ->numeric()
                    ->required()
                    ->suffix(config('bookstore.currency_symbol'))
                    ->helperText('Used when no emirate-specific rate applies.'),
            ])->columns(2),
            Forms\Components\Section::make('Currency display')->schema([
                Forms\Components\TextInput::make('currency_symbol')
                    ->required()
                    ->maxLength(10)
                    ->default('AED'),
                Forms\Components\TextInput::make('currency_code')
                    ->label('ISO code (Stripe)')
                    ->required()
                    ->maxLength(10)
                    ->default('AED'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('free_shipping_threshold')->label('Free from')->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('default_shipping_rate')->label('Default ship rate')->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('currency_symbol'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->since(),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStoreSettings::route('/'),
            'create' => Pages\CreateStoreSetting::route('/create'),
            'edit'   => Pages\EditStoreSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return StoreSetting::query()->count() === 0;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
