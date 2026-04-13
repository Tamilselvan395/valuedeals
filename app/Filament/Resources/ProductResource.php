<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model           = Product::class;
    protected static ?string $navigationIcon  = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationBadge(): ?string
    {
        $oos = static::getModel()::where('stock', 0)->count();
        return $oos > 0 ? "{$oos} OOS" : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Book Details')->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()->maxLength(255)->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()->unique(Product::class, 'slug', ignoreRecord: true)->maxLength(255),
                    Forms\Components\TextInput::make('author')->maxLength(255),
                    Forms\Components\TextInput::make('isbn')->label('ISBN')->maxLength(20),
                    Forms\Components\Textarea::make('description')->label('Short Description')->rows(3)->columnSpanFull(),
                    Forms\Components\RichEditor::make('full_description')
                        ->label('Full Description')
                        ->toolbarButtons(['bold','italic','underline','bulletList','orderedList','h2','h3','link','undo','redo'])
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Pricing & Stock')->schema([
                    Forms\Components\TextInput::make('price')->required()->numeric()->prefix(config('bookstore.currency_symbol'))->step(0.01),
                    Forms\Components\TextInput::make('discount_price')->numeric()->prefix(config('bookstore.currency_symbol'))->step(0.01)
                        ->helperText('Leave blank for no discount.'),
                    Forms\Components\TextInput::make('stock')->required()->numeric()->integer()->minValue(0)->default(0),
                ])->columns(3),

                Forms\Components\Section::make('Cover & Gallery')->schema([
                    Forms\Components\FileUpload::make('cover_image')->label('Cover Image')
                        ->image()->disk('public')->directory('products/covers')->imageResizeMode('cover')->columnSpanFull(),
                    Forms\Components\Repeater::make('images')->label('Gallery Images')->relationship()
                        ->schema([
                            Forms\Components\FileUpload::make('image_path')->label('Image')->image()
                                ->disk('public')->directory('products/gallery')->required(),
                            Forms\Components\TextInput::make('alt_text')->label('Alt Text')->placeholder('Describe the image'),
                            Forms\Components\TextInput::make('sort_order')->numeric()->default(0)->label('Sort Order'),
                        ])
                        ->columns(3)->reorderable()->collapsible()->columnSpanFull(),
                ]),

            ])->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Status')->schema([
                    Forms\Components\Toggle::make('is_active')->label('Active / Published')->default(true),
                    Forms\Components\Toggle::make('is_featured')->label('Featured on Homepage')->default(false),
                ]),

                Forms\Components\Section::make('Classification')->schema([
                    Forms\Components\Select::make('category_id')->label('Category')
                        ->options(Category::where('is_active', true)->pluck('name', 'id'))
                        ->required()->searchable()->preload(),
                    Forms\Components\Select::make('tags')->label('Tags')
                        ->relationship('tags', 'name')->multiple()->searchable()->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\TextInput::make('slug')->required()->unique(),
                        ]),
                ]),

                Forms\Components\Section::make('SEO')->schema([
                    Forms\Components\TextInput::make('meta_title')->label('Meta Title')->maxLength(70)
                        ->placeholder('Leave blank to use book title'),
                    Forms\Components\Textarea::make('meta_description')->label('Meta Description')
                        ->rows(3)->maxLength(160)->placeholder('Leave blank to use short description'),
                ]),

            ])->columnSpan(['lg' => 1]),

        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->disk('public')->label('Cover')->height(60)->width(45),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->weight('bold')->limit(35)
                    ->description(fn (Product $record) => $record->author ? 'by ' . $record->author : null),
                Tables\Columns\TextColumn::make('category.name')->badge()->color('warning')->sortable(),
                Tables\Columns\TextColumn::make('price')->money(config('bookstore.currency_code', 'AED'))->sortable(),
                Tables\Columns\TextColumn::make('discount_price')->label('Sale Price')->money(config('bookstore.currency_code', 'AED'))->sortable()->placeholder('—'),
                Tables\Columns\TextColumn::make('stock')->sortable()->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 5   => 'warning',
                        default      => 'success',
                    }),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->label('Featured')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
                Tables\Filters\Filter::make('out_of_stock')->label('Out of Stock')
                    ->query(fn ($query) => $query->where('stock', 0))->toggle(),
                Tables\Filters\Filter::make('low_stock')->label('Low Stock (< 5)')
                    ->query(fn ($query) => $query->where('stock', '>', 0)->where('stock', '<', 5))->toggle(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_active')->label('Mark as Active')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('mark_inactive')->label('Mark as Inactive')
                        ->icon('heroicon-o-x-circle')->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
