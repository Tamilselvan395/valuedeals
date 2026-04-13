<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbandonedCartResource\Pages;
use App\Models\AbandonedCart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AbandonedCartResource extends Resource
{
    protected static ?string $model           = AbandonedCart::class;
    protected static ?string $navigationIcon  = 'heroicon-o-archive-box-x-mark';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $navigationLabel = 'Abandoned Carts';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Cart Details')->schema([
                Forms\Components\TextInput::make('user.name')->disabled()->label('User'),
                Forms\Components\TextInput::make('email')->disabled(),
                Forms\Components\TextInput::make('session_id')->disabled()->label('Session ID'),
                Forms\Components\TextInput::make('cart_total')->prefix(config('bookstore.currency_symbol'))->disabled(),
                Forms\Components\TextInput::make('item_count')->disabled()->label('Items'),
                Forms\Components\DateTimePicker::make('last_activity_at')->disabled()->label('Last Active'),
                Forms\Components\KeyValue::make('cart_data')->disabled()->label('Cart Items (JSON)')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('User')->placeholder('Guest')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->placeholder('—')->copyable(),
                Tables\Columns\TextColumn::make('item_count')->label('Items')->badge()->color('warning'),
                Tables\Columns\TextColumn::make('cart_total')->label('Value')->money(config('bookstore.currency_code', 'AED'))->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('last_activity_at')->label('Last Active')->dateTime('d M Y, h:i A')->sortable(),
            ])
            ->defaultSort('last_activity_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('guests_only')->label('Guests Only')
                    ->query(fn ($query) => $query->whereNull('user_id'))->toggle(),
                Tables\Filters\Filter::make('high_value')->label('High value (500+ ' . config('bookstore.currency_code', 'AED') . ')')
                    ->query(fn ($query) => $query->where('cart_total', '>=', 500))->toggle(),
            ])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbandonedCarts::route('/'),
            'view'  => Pages\ViewAbandonedCart::route('/{record}'),
        ];
    }
}
