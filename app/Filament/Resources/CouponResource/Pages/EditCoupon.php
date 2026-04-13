<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\EditRecord;

class EditCoupon extends EditRecord
{
    protected static string $resource = CouponResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['code'])) {
            $data['code'] = mb_strtoupper(trim((string) $data['code']));
        }

        if (array_key_exists('max_uses', $data) && ($data['max_uses'] === '' || $data['max_uses'] === null)) {
            $data['max_uses'] = null;
        }

        return $data;
    }
}
