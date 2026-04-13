<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model           = Lead::class;
    protected static ?string $navigationIcon  = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationBadge(): ?string
    {
        $unread = Lead::where('is_read', false)->count();
        return $unread > 0 ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contact Details')->schema([
                Forms\Components\TextInput::make('name')->disabled(),
                Forms\Components\TextInput::make('email')->disabled(),
                Forms\Components\TextInput::make('phone')->disabled(),
                // Forms\Components\TextInput::make('subject')->disabled()->columnSpanFull(),
                Forms\Components\Textarea::make('message')->disabled()->rows(5)->columnSpanFull(),
                Forms\Components\Toggle::make('is_read')->label('Mark as Read'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_read')->label('Read')->boolean()
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')->falseColor('warning'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('phone')->placeholder('—'),
                // Tables\Columns\TextColumn::make('subject')->limit(40)->placeholder('—'),
                Tables\Columns\TextColumn::make('message')->limit(60)->color('gray'),
                Tables\Columns\TextColumn::make('created_at')->label('Received')->dateTime('d M Y, h:i A')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([Tables\Filters\TernaryFilter::make('is_read')->label('Read Status')])
            ->actions([
                Tables\Actions\EditAction::make()->label('View'),
                Tables\Actions\Action::make('mark_read')->label('Mark Read')
                    ->icon('heroicon-o-check')->color('success')
                    ->action(fn (Lead $record) => $record->update(['is_read' => true]))
                    ->visible(fn (Lead $record) => ! $record->is_read),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_all_read')->label('Mark as Read')
                        ->icon('heroicon-o-check-badge')
                        ->action(fn ($records) => $records->each->update(['is_read' => true]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'edit'  => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
