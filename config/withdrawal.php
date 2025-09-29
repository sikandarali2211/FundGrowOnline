<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Withdrawal Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings for the automated withdrawal
    | system including limits, fees, and security settings.
    |
    */

    'min_amount' => env('WITHDRAWAL_MIN_AMOUNT', 1.0),
    'max_amount' => env('WITHDRAWAL_MAX_AMOUNT', 10000.0),
    'daily_limit' => env('WITHDRAWAL_DAILY_LIMIT', 50000.0),
    'processing_fee' => env('WITHDRAWAL_PROCESSING_FEE', 0.0),
    'processing_fee_percentage' => env('WITHDRAWAL_PROCESSING_FEE_PERCENTAGE', 0.0),

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'require_2fa' => env('WITHDRAWAL_REQUIRE_2FA', true),
    'max_retries' => env('WITHDRAWAL_MAX_RETRIES', 3),
    'retry_delay' => env('WITHDRAWAL_RETRY_DELAY', 30), // seconds
    'timeout_hours' => env('WITHDRAWAL_TIMEOUT_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Blockchain Settings
    |--------------------------------------------------------------------------
    */
    'gas_limit' => env('WITHDRAWAL_GAS_LIMIT', 100000),
    'gas_price' => env('WITHDRAWAL_GAS_PRICE', '5000000000'), // 5 Gwei
    'confirmation_blocks' => env('WITHDRAWAL_CONFIRMATION_BLOCKS', 3),

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notify_on_success' => env('WITHDRAWAL_NOTIFY_SUCCESS', true),
    'notify_on_failure' => env('WITHDRAWAL_NOTIFY_FAILURE', true),
    'notify_admin' => env('WITHDRAWAL_NOTIFY_ADMIN', true),

    /*
    |--------------------------------------------------------------------------
    | Maintenance Settings
    |--------------------------------------------------------------------------
    */
    'maintenance_mode' => env('WITHDRAWAL_MAINTENANCE_MODE', false),
    'maintenance_message' => env('WITHDRAWAL_MAINTENANCE_MESSAGE', 'Withdrawal system is temporarily under maintenance. Please try again later.'),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit_per_hour' => env('WITHDRAWAL_RATE_LIMIT_PER_HOUR', 10),
    'rate_limit_per_day' => env('WITHDRAWAL_RATE_LIMIT_PER_DAY', 50),
];
