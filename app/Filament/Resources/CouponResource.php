<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->maxLength(50)
                ->placeholder('Leave empty to auto-generate')
                ->helperText('Stored in uppercase. Empty = random code on save.'),
            Forms\Components\TextInput::make('description')->maxLength(255)->columnSpanFull(),
            Forms\Components\Select::make('discount_type')
                ->options([
                    Coupon::TYPE_PERCENT => 'Percent off',
                    Coupon::TYPE_FIXED   => 'Fixed amount off',
                ])
                ->required()
                ->native(false),
            Forms\Components\TextInput::make('discount_value')
                ->numeric()
                ->required()
                ->suffix(fn (Forms\Get $get): ?string => $get('discount_type') === Coupon::TYPE_PERCENT ? '%' : config('bookstore.currency_symbol')),
            Forms\Components\TextInput::make('min_order_amount')
                ->label('Minimum order subtotal')
                ->numeric()
                ->default(0)
                ->suffix(config('bookstore.currency_symbol')),
            Forms\Components\TextInput::make('max_uses')
                ->label('Max redemptions (empty = unlimited)')
                ->numeric()
                ->minValue(1),
            Forms\Components\DateTimePicker::make('expires_at')->native(false),
            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable()->copyable()->weight('bold'),
                Tables\Columns\TextColumn::make('discount_type')->badge(),
                Tables\Columns\TextColumn::make('discount_value')->label('Value')->formatStateUsing(function (Coupon $record): string {
                    if ($record->discount_type === Coupon::TYPE_PERCENT) {
                        return $record->discount_value.'%';
                    }

                    return config('bookstore.currency_symbol').number_format((float) $record->discount_value, 2);
                }),
                Tables\Columns\TextColumn::make('uses_count')->label('Uses'),
                Tables\Columns\TextColumn::make('max_uses')->placeholder('∞'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('expires_at')->dateTime()->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit'   => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
