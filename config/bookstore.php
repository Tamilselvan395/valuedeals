<?php

return [
    'free_shipping_threshold' => env('FREE_SHIPPING_THRESHOLD', 99),
    'flat_shipping_rate'      => env('FLAT_SHIPPING_RATE', 15),
    'currency_code'           => env('CURRENCY_CODE', 'AED'),
    'store_name'              => env('APP_NAME', 'BookStore'),
    'store_email'             => env('MAIL_FROM_ADDRESS', 'hello@bookstore.com'),
    'store_phone'             => env('STORE_PHONE', '+971 50 000 0000'),
    'store_address'           => env('STORE_ADDRESS', 'Dubai, UAE'),
    'currency_symbol'         => env('CURRENCY_SYMBOL', 'AED'),
];
