<?php

return [
    'defaults' => [
        'app_name' => 'FarmHisab',
        'business_name' => 'FarmHisab Poultry Farm',
        'owner_name' => null,
        'phone' => null,
        'email' => null,
        'address' => null,
        'default_locale' => 'bn',
        'timezone' => 'Asia/Dhaka',
        'currency_code' => 'BDT',
        'currency_symbol' => 'Tk',
        'fiscal_year_start_month' => 'July',
        'low_stock_alert_enabled' => '1',
        'due_alert_enabled' => '1',
    ],

    'groups' => [
        'general' => [
            'app_name',
            'business_name',
            'owner_name',
            'phone',
            'email',
            'address',
        ],
        'localization' => [
            'default_locale',
            'timezone',
            'currency_code',
            'currency_symbol',
            'fiscal_year_start_month',
        ],
        'notifications' => [
            'low_stock_alert_enabled',
            'due_alert_enabled',
        ],
    ],
];
