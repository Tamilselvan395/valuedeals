<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model           = BlogPost::class;
    protected static ?string $navigationIcon  = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int    $navigationSort  = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Post Content')->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()->maxLength(255)->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()->unique(BlogPost::class, 'slug', ignoreRecord: true)->maxLength(255),
                    Forms\Components\Textarea::make('excerpt')->rows(2)->maxLength(500)->columnSpanFull(),
                    Forms\Components\RichEditor::make('content')->required()
                        ->toolbarButtons(['bold','italic','underline','strike','h2','h3','blockquote','bulletList','orderedList','link','attachFiles','undo','redo'])
                        ->fileAttachmentsDirectory('blog/attachments')->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('SEO')->schema([
                    Forms\Components\TextInput::make('meta_title')->label('Meta Title')->maxLength(70),
                    Forms\Components\Textarea::make('meta_description')->label('Meta Description')
                        ->rows(2)->maxLength(160)->columnSpanFull(),
                ])->columns(1),

            ])->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Publish Settings')->schema([
                    Forms\Components\Toggle::make('is_published')->label('Published')->default(true)->live(),
                    Forms\Components\DateTimePicker::make('published_at')->label('Publish Date')->default(now())
                        ->visible(fn (Forms\Get $get) => $get('is_published')),
                ]),

                Forms\Components\Section::make('Cover Image')->schema([
                    Forms\Components\FileUpload::make('cover_image')
                        ->image()->directory('blog/covers')->imageResizeMode('cover')->columnSpanFull(),
                ]),

            ])->columnSpan(['lg' => 1]),

        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->label('Cover')->height(50),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->weight('bold')->limit(45),
                Tables\Columns\TextColumn::make('author.name')->label('Author')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Published')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->label('Publish Date')->dateTime('d M Y')->sortable()->placeholder('Draft'),
            ])
            ->filters([Tables\Filters\TernaryFilter::make('is_published')->label('Published')])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view')->label('View Post')
                    ->icon('heroicon-o-arrow-top-right-on-square')->color('gray')
                    ->url(fn (BlogPost $record) => route('blog.show', $record->slug))->openUrlInNewTab()
                    ->visible(fn (BlogPost $record) => $record->is_published),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')->label('Publish Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_published'=>true,'published_at'=>now()]))
                        ->requiresConfirmation()->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
