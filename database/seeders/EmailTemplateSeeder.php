<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\Admin;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user (or create one if none exists)
        $admin = Admin::first();
        if (!$admin) {
            $admin = Admin::create([
                'name' => 'System Admin',
                'email' => 'admin@netonyou.com',
                'password' => bcrypt('password'),
                'role' => 'super_admin'
            ]);
        }

        $templates = [
            [
                'name' => 'welcome_email',
                'language' => 'en',
                'subject' => 'Welcome to Net On You, {name}!',
                'body' => "Hello {name},

Welcome to Net On You! Your account has been successfully created and you're now part of our community.

Your current plan: {plan}
Account expires: {expiry}

We're excited to have you on board. Here are some things you can do to get started:

• Browse our magazine collection
• Set up your profile preferences
• Invite friends and earn commissions
• Check out our latest content

If you have any questions, feel free to contact our support team at {support_email}.

Best regards,
The Net On You Team",
                'variables' => ['name', 'email', 'plan', 'expiry', 'support_email']
            ],
            [
                'name' => 'welcome_email',
                'language' => 'ur',
                'subject' => 'Net On You میں خوش آمدید، {name}!',
                'body' => "السلام علیکم {name}،

Net On You میں خوش آمدید! آپ کا اکاؤنٹ کامیابی سے بنا دیا گیا ہے اور آپ اب ہماری کمیونٹی کا حصہ ہیں۔

آپ کا موجودہ پلان: {plan}
اکاؤنٹ کی میعاد ختم: {expiry}

ہمیں آپ کو شامل کرنے پر خوشی ہے۔ یہاں کچھ چیزیں ہیں جو آپ شروع کرنے کے لیے کر سکتے ہیں:

• ہماری میگزین کی مجموعہ کو براؤز کریں
• اپنی پروفائل کی ترجیحات سیٹ کریں
• دوستوں کو مدعو کریں اور کمیشن کمائیں
• ہماری تازہ ترین مواد دیکھیں

اگر آپ کے کوئی سوالات ہیں تو {support_email} پر ہماری سپورٹ ٹیم سے رابطہ کریں۔

بہترین خواہشات کے ساتھ،
Net On You ٹیم",
                'variables' => ['name', 'email', 'plan', 'expiry', 'support_email']
            ],
            [
                'name' => 'password_reset',
                'language' => 'en',
                'subject' => 'Password Reset Request - Net On You',
                'body' => "Hello {name},

You recently requested a password reset for your Net On You account.

Click the link below to reset your password:

{reset_link}

This link will expire in 60 minutes for security reasons.

If you didn't request this password reset, please ignore this email. Your password will remain unchanged.

If you have any questions, contact our support team at {support_email}.

Best regards,
The Net On You Team",
                'variables' => ['name', 'reset_link', 'support_email']
            ],
            [
                'name' => 'password_reset',
                'language' => 'ur',
                'subject' => 'پاس ورڈ ریسیٹ کی درخواست - Net On You',
                'body' => "السلام علیکم {name}،

آپ نے حال ہی میں اپنے Net On You اکاؤنٹ کے لیے پاس ورڈ ریسیٹ کی درخواست کی ہے۔

اپنا پاس ورڈ ریسیٹ کرنے کے لیے نیچے دیے گئے لنک پر کلک کریں:

{reset_link}

یہ لنک سیکیورٹی کی وجوہات سے 60 منٹ میں ختم ہو جائے گا۔

اگر آپ نے یہ پاس ورڈ ریسیٹ نہیں مانگا تو براہ کرم اس ای میل کو نظر انداز کریں۔ آپ کا پاس ورڈ تبدیل نہیں ہوگا۔

اگر آپ کے کوئی سوالات ہیں تو {support_email} پر ہماری سپورٹ ٹیم سے رابطہ کریں۔

بہترین خواہشات کے ساتھ،
Net On You ٹیم",
                'variables' => ['name', 'reset_link', 'support_email']
            ],
            [
                'name' => 'payment_confirmation',
                'language' => 'en',
                'subject' => 'Payment Confirmation - Transaction #{transaction_id}',
                'body' => "Hello {name},

Thank you for your payment! Your transaction has been successfully processed.

Transaction Details:
• Transaction ID: {transaction_id}
• Amount: {amount}
• Plan: {plan}
• Date: {date}

Your subscription has been activated and you now have access to all premium features.

If you have any questions about your payment or subscription, please contact our support team at {support_email}.

Best regards,
The Net On You Team",
                'variables' => ['name', 'amount', 'plan', 'transaction_id', 'date', 'support_email']
            ],
            [
                'name' => 'commission_payout',
                'language' => 'en',
                'subject' => 'Commission Payout - {payout_id}',
                'body' => "Hello {name},

Great news! Your commission payout has been processed successfully.

Payout Details:
• Payout ID: {payout_id}
• Amount: {amount}
• Date: {date}

Your earnings have been transferred to your account. Thank you for being a valuable member of our referral program!

Keep up the great work and continue earning commissions by referring more users to Net On You.

If you have any questions, contact our support team at {support_email}.

Best regards,
The Net On You Team",
                'variables' => ['name', 'amount', 'payout_id', 'date', 'support_email']
            ],
            [
                'name' => 'newsletter_announcement',
                'language' => 'en',
                'subject' => 'Net On You Newsletter - {date}',
                'body' => "Hello {name},

Here's what's new at Net On You this week:

📚 New Magazines Added
We've added several new magazines to our collection. Check them out!

🎯 Featured Content
Discover our handpicked content recommendations just for you.

💰 Referral Program Update
Earn more with our enhanced referral program. Invite friends and get rewarded!

📱 Mobile App Updates
Our mobile app has been updated with new features and improvements.

Stay connected with us for more updates and exclusive content.

Best regards,
The Net On You Team

P.S. Don't forget to check out our latest content!",
                'variables' => ['name', 'date', 'company_name']
            ]
        ];

        foreach ($templates as $templateData) {
            EmailTemplate::create([
                'name' => $templateData['name'],
                'language' => $templateData['language'],
                'subject' => $templateData['subject'],
                'body' => $templateData['body'],
                'variables' => $templateData['variables'],
                'created_by_admin_id' => $admin->id,
                'updated_by_admin_id' => $admin->id,
            ]);
        }

        $this->command->info('Email templates seeded successfully!');
    }
}
