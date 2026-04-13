<?php

namespace App\Filament\Resources\EmirateShippingRateResource\Pages;

use App\Filament\Resources\EmirateShippingRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmirateShippingRates extends ListRecords
{
    protected static string $resource = EmirateShippingRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
