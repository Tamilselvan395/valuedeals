<?php

namespace App\Filament\Pages;

use App\Models\StoreSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageStoreSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Store Configuration';
    protected static ?string $title = 'Store Configuration';

    protected static string $view = 'filament.pages.manage-store-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = StoreSetting::current();
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Site Logo')
                            ->image()
                            ->directory('settings/logo'),
                        Forms\Components\FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->image()
                            ->directory('settings/favicon'),
                        Forms\Components\TextInput::make('mobile_number')
                            ->label('Contact Number')
                            ->tel(),
                        Forms\Components\Textarea::make('address')
                            ->label('Physical Address')
                            ->rows(3),
                        Forms\Components\Textarea::make('map_embed')
                            ->label('Google Maps Embed Code')
                            ->rows(4),
                    ])->columns(2),
                    
                    Forms\Components\Tabs\Tab::make('Social Media')->schema([
                        Forms\Components\TextInput::make('facebook_url')->url()->label('Facebook URL'),
                        Forms\Components\TextInput::make('instagram_url')->url()->label('Instagram URL'),
                        Forms\Components\TextInput::make('twitter_url')->url()->label('Twitter/X URL'),
                    ])->columns(1),

                    Forms\Components\Tabs\Tab::make('SEO Config')->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('Global SEO Title (Suffix)'),
                        Forms\Components\FileUpload::make('seo_og_image')
                            ->label('Default OpenGraph Image')
                            ->image()
                            ->directory('settings/seo'),
                        Forms\Components\FileUpload::make('seo_twitter_image')
                            ->label('Default Twitter Card Image')
                            ->image()
                            ->directory('settings/seo'),
                    ])->columns(1),
                ])->columnSpanFull()
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = StoreSetting::current();
        $settings->update($this->form->getState());

        Notification::make()
            ->title('Settings safely updated')
            ->success()
            ->send();
    }
}
