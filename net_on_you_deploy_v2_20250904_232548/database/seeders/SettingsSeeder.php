<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_title',
                'value' => 'NetOnYou',
                'type' => 'string',
                'description' => 'The main title displayed on the website',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'default_currency',
                'value' => 'USD',
                'type' => 'string',
                'description' => 'Default currency for transactions',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'default_language',
                'value' => 'en',
                'type' => 'string',
                'description' => 'Default language for new users',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'timezone',
                'value' => 'UTC',
                'type' => 'string',
                'description' => 'Default timezone for the application',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'payment_gateway',
                'value' => 'stripe',
                'type' => 'string',
                'description' => 'Primary payment processing method',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'subscription_price',
                'value' => '9.99',
                'type' => 'decimal',
                'description' => 'Monthly subscription price in USD',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@netonyou.com',
                'type' => 'string',
                'description' => 'Default sender email address',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'NetOnYou',
                'type' => 'string',
                'description' => 'Default sender name',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'stripe_public_key',
                'value' => '',
                'type' => 'string',
                'description' => 'Stripe public key for payment processing',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'stripe_secret_key',
                'value' => '',
                'type' => 'string',
                'description' => 'Stripe secret key for payment processing',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'paypal_client_id',
                'value' => '',
                'type' => 'string',
                'description' => 'PayPal client ID for payment processing',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'paypal_secret',
                'value' => '',
                'type' => 'string',
                'description' => 'PayPal secret for payment processing',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'coinpayments_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Enable CoinPayments cryptocurrency payments',
                'updated_by_admin_id' => 1,
            ],
            [
                'key' => 'coinpayments_currency',
                'value' => 'USDT.TRC20',
                'type' => 'string',
                'description' => 'Default cryptocurrency for CoinPayments',
                'updated_by_admin_id' => 1,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

