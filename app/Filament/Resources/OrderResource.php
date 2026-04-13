<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model           = Order::class;
    protected static ?string $navigationIcon  = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Order::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Order Items')->schema([
                    Forms\Components\Repeater::make('items')->relationship()
                        ->schema([
                            Forms\Components\TextInput::make('product_title')->disabled()->label('Product'),
                            Forms\Components\TextInput::make('unit_price')->prefix(config('bookstore.currency_symbol'))->disabled(),
                            Forms\Components\TextInput::make('quantity')->disabled(),
                            Forms\Components\TextInput::make('subtotal')->prefix(config('bookstore.currency_symbol'))->disabled(),
                        ])
                        ->columns(4)->deletable(false)->addable(false),
                ]),

                Forms\Components\Section::make('Shipping Address')->schema([
                    Forms\Components\TextInput::make('shipping_name')->disabled(),
                    Forms\Components\TextInput::make('shipping_phone')->disabled(),
                    Forms\Components\TextInput::make('shipping_email')->disabled(),
                    Forms\Components\Textarea::make('shipping_address')->disabled()->columnSpanFull(),
                    Forms\Components\TextInput::make('shipping_city')->label('Area / district')->disabled(),
                    Forms\Components\TextInput::make('shipping_state')->label('Emirate (slug)')->disabled(),
                    Forms\Components\TextInput::make('shipping_pincode')->disabled(),
                    Forms\Components\TextInput::make('shipping_country')->disabled(),
                ])->columns(2),

            ])->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Order Status')->schema([
                    Forms\Components\Select::make('status')
                        ->options(['pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'])
                        ->required()->native(false),
                    Forms\Components\Select::make('payment_status')
                        ->options(['unpaid'=>'Unpaid','paid'=>'Paid','failed'=>'Failed'])
                        ->required()->native(false),
                ]),

                Forms\Components\Section::make('Order Summary')->schema([
                    Forms\Components\TextInput::make('order_number')->disabled(),
                    Forms\Components\TextInput::make('coupon_code')->disabled(),
                    Forms\Components\TextInput::make('discount_amount')->prefix(config('bookstore.currency_symbol'))->disabled(),
                    Forms\Components\TextInput::make('subtotal')->prefix(config('bookstore.currency_symbol'))->disabled(),
                    Forms\Components\TextInput::make('shipping_cost')->prefix(config('bookstore.currency_symbol'))->disabled(),
                    Forms\Components\TextInput::make('total')->prefix(config('bookstore.currency_symbol'))->disabled(),
                    Forms\Components\TextInput::make('payment_method')->disabled(),
                ]),

                Forms\Components\Section::make('Customer')->schema([
                    Forms\Components\TextInput::make('user.name')->disabled()->label('Name'),
                    Forms\Components\TextInput::make('user.email')->disabled()->label('Email'),
                ]),

                Forms\Components\Section::make('Notes')->schema([
                    Forms\Components\Textarea::make('notes')->rows(3)->disabled(),
                ]),

            ])->columnSpan(['lg' => 1]),

        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('Order #')->searchable()->sortable()->weight('bold')->copyable(),
                Tables\Columns\TextColumn::make('user.name')->label('Customer')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('shipping_city')->label('City')->toggleable(),
                Tables\Columns\TextColumn::make('total')->money(config('bookstore.currency_code', 'AED'))->sortable()->weight('bold'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning'=>'pending','info'=>'processing','primary'=>'shipped','success'=>'delivered','danger'=>'cancelled'])
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('payment_status')->label('Payment')
                    ->colors(['danger'=>'unpaid','success'=>'paid','warning'=>'failed']),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d M Y, h:i A')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled']),
                Tables\Filters\SelectFilter::make('payment_status')->options(['unpaid'=>'Unpaid','paid'=>'Paid','failed'=>'Failed']),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'],  fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['until'], fn ($q, $d) => $q->whereDate('created_at', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Manage'),
                Tables\Actions\Action::make('invoice')->label('Invoice')
                    ->icon('heroicon-o-document-arrow-down')->color('gray')
                    ->url(fn (Order $record) => route('orders.invoice', $record))->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_processing')->label('Mark Processing')
                        ->icon('heroicon-o-arrow-path')
                        ->action(fn ($records) => $records->each->update(['status'=>'processing']))
                        ->requiresConfirmation()->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('mark_shipped')->label('Mark Shipped')
                        ->icon('heroicon-o-truck')
                        ->action(fn ($records) => $records->each->update(['status'=>'shipped']))
                        ->requiresConfirmation()->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('mark_delivered')->label('Mark Delivered')
                        ->icon('heroicon-o-check-badge')->color('success')
                        ->action(fn ($records) => $records->each->update(['status'=>'delivered','payment_status'=>'paid']))
                        ->requiresConfirmation()->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit'  => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
