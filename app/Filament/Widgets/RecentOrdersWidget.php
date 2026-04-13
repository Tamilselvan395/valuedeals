<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort         = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading   = 'Recent Orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')->searchable()->weight('bold')
                    ->url(fn (Order $record) => OrderResource::getUrl('edit', ['record' => $record])),
                Tables\Columns\TextColumn::make('user.name')->label('Customer')->searchable(),
                Tables\Columns\TextColumn::make('total')->money(config('bookstore.currency_code', 'AED'))->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'processing',
                        'primary' => 'shipped',
                        'success' => 'delivered',
                        'danger'  => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d M Y, h:i A')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Order $record) => OrderResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-eye')->color('gray'),
            ]);
    }
}
