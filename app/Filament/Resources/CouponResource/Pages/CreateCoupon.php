<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use App\Models\Coupon;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $code = isset($data['code']) ? trim((string) $data['code']) : '';
        if ($code === '') {
            $data['code'] = Coupon::generateUniqueCode(10);
        } else {
            $data['code'] = mb_strtoupper($code);
        }

        if (array_key_exists('max_uses', $data) && ($data['max_uses'] === '' || $data['max_uses'] === null)) {
            $data['max_uses'] = null;
        }

        return $data;
    }
}
