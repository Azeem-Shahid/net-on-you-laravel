<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Referral;
use App\Models\Commission;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\Magazine;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use App\Models\Language;
use App\Models\Translation;
use App\Models\Contract;
use App\Models\PayoutBatch;
use App\Models\PayoutBatchItem;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\SecurityPolicy;
use App\Models\ScheduledCommand;
use App\Models\CommandLog;
use App\Models\AuditLog;
use App\Models\ReportCache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EnhancedDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting enhanced dummy data seeding...');
        
        // Create User 1 as the main referrer with extensive referral network
        $this->createUser1ReferralNetwork();
        
        // Create additional users and referrals
        $this->createAdditionalUsers();
        
        // Create comprehensive transaction data
        $this->createTransactionData();
        
        // Create commission data
        $this->createCommissionData();
        
        // Create magazine data
        $this->createMagazineData();
        
        // Create email system data
        $this->createEmailData();
        
        // Create language and translation data
        $this->createLanguageData();
        
        // Create contract data
        $this->createContractData();
        
        // Create payout data
        $this->createPayoutData();
        
        // Create admin data
        $this->createAdminData();
        
        // Create settings data
        $this->createSettingsData();
        
        // Create security data
        $this->createSecurityData();
        
        // Create system data
        $this->createSystemData();
        
        $this->command->info('Enhanced dummy data seeding completed successfully!');
    }
    
    private function createUser1ReferralNetwork()
    {
        $this->command->info('Creating User 1 referral network...');
        
        // Create User 1 as the main referrer (or get existing)
        $user1 = User::firstOrCreate(
            ['email' => 'alex.johnson@example.com'],
            [
                'name' => 'Alex Johnson',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'en',
                'wallet_address' => '0x1234567890abcdef1234567890abcdef12345678',
                'email_verified_at' => now(),
            ]
        );
        
        // Create subscription for User 1
        $user1Subscription = Subscription::firstOrCreate(
            ['user_id' => $user1->id],
            [
                'plan_name' => 'Premium',
                'start_date' => now()->subMonths(6),
                'end_date' => now()->addMonths(6),
                'status' => 'active',
            ]
        );
        
        // Create transaction for User 1
        $user1Transaction = Transaction::firstOrCreate(
            ['user_id' => $user1->id, 'notes' => 'Premium subscription payment'],
            [
                'amount' => 99.99,
                'currency' => 'USD',
                'gateway' => 'credit_card',
                'status' => 'completed',
                'transaction_hash' => '0x' . strtoupper(uniqid()),
            ]
        );
        
        // Create Level 1 referrals (direct referrals)
        $level1Users = [];
        for ($i = 1; $i <= 10; $i++) {
            $level1User = User::create([
                'name' => "Level1 User {$i}",
                'email' => "level1user{$i}@example.com",
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'en',
                'wallet_address' => '0x' . str_pad(dechex(rand(1000000000, 9999999999)), 40, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
                'referrer_id' => $user1->id,

            ]);
            
            $level1Users[] = $level1User;
            
            // Create subscription for Level 1 user
            $subscription = Subscription::create([
                'user_id' => $level1User->id,
                'plan_name' => rand(0, 1) ? 'Basic' : 'Premium',
                'start_date' => now()->subDays(rand(1, 180)),
                'end_date' => now()->addDays(rand(1, 365)),
                'status' => 'active',
            ]);
            
            // Create transaction for Level 1 user
            $transaction = Transaction::create([
                'user_id' => $level1User->id,
                'amount' => $subscription->plan_name === 'Premium' ? 99.99 : 49.99,
                'currency' => 'USD',
                'gateway' => rand(0, 1) ? 'credit_card' : 'crypto',
                'status' => 'completed',
                'transaction_hash' => '0x' . strtoupper(uniqid()),
                'notes' => "Subscription payment for {$subscription->plan_name}",
            ]);
            
            // Create commission for User 1
            $commissionAmount = $transaction->amount * 0.10; // 10% commission
            Commission::create([
                'earner_user_id' => $user1->id,
                'source_user_id' => $level1User->id,
                'transaction_id' => $transaction->id,
                'level' => 1,
                'amount' => $commissionAmount,
                'month' => $subscription->start_date->format('Y-m'),
                'eligibility' => 'eligible',
                'payout_status' => rand(0, 1) ? 'pending' : 'paid',
            ]);
            
            // Create referral record
            Referral::create([
                'user_id' => $user1->id,
                'referred_user_id' => $level1User->id,
                'level' => 1,
            ]);
        }
        
        // Create Level 2 referrals (referrals of Level 1 users)
        foreach ($level1Users as $index => $level1User) {
            if ($index < 5) { // Only first 5 Level 1 users have Level 2 referrals
                for ($j = 1; $j <= 3; $j++) {
                    $level2User = User::create([
                        'name' => "Level2 User {$index}-{$j}",
                        'email' => "level2user{$index}_{$j}@example.com",
                        'password' => Hash::make('password123'),
                        'role' => 'user',
                        'status' => 'active',
                        'language' => 'en',
                        'wallet_address' => '0x' . str_pad(dechex(rand(1000000000, 9999999999)), 40, '0', STR_PAD_LEFT),
                        'email_verified_at' => now(),
                        'referrer_id' => $level1User->id,

                    ]);
                    
                    // Create subscription for Level 2 user
                    $subscription = Subscription::create([
                        'user_id' => $level2User->id,
                        'plan_name' => rand(0, 1) ? 'Basic' : 'Premium',
                        'start_date' => now()->subDays(rand(1, 90)),
                        'end_date' => now()->addDays(rand(1, 300)),
                        'status' => 'active',
                    ]);
                    
                    // Create transaction for Level 2 user
                    $transaction = Transaction::create([
                        'user_id' => $level2User->id,
                        'amount' => $subscription->plan_name === 'Premium' ? 99.99 : 49.99,
                        'currency' => 'USD',
                        'gateway' => rand(0, 1) ? 'credit_card' : 'crypto',
                        'status' => 'completed',
                        'transaction_hash' => '0x' . strtoupper(uniqid()),
                        'notes' => "Subscription payment for {$subscription->plan_name}",
                    ]);
                    
                    // Create commission for Level 1 user (5% commission)
                    $commissionAmount = $transaction->amount * 0.05;
                    Commission::create([
                        'earner_user_id' => $level1User->id,
                        'source_user_id' => $level2User->id,
                        'transaction_id' => $transaction->id,
                        'level' => 2,
                        'amount' => $commissionAmount,
                        'month' => $subscription->start_date->format('Y-m'),
                        'eligibility' => 'eligible',
                        'payout_status' => 'pending',
                    ]);
                    
                    // Create commission for User 1 (2% commission)
                    $commissionAmount = $transaction->amount * 0.02;
                    Commission::create([
                        'earner_user_id' => $user1->id,
                        'source_user_id' => $level2User->id,
                        'transaction_id' => $transaction->id,
                        'level' => 2,
                        'amount' => $commissionAmount,
                        'month' => $subscription->start_date->format('Y-m'),
                        'eligibility' => 'eligible',
                        'payout_status' => 'pending',
                    ]);
                    
                    // Create referral records
                    Referral::create([
                        'user_id' => $level1User->id,
                        'referred_user_id' => $level2User->id,
                        'level' => 1,
                    ]);
                    
                    Referral::create([
                        'user_id' => $user1->id,
                        'referred_user_id' => $level2User->id,
                        'level' => 2,
                    ]);
                }
            }
        }
        
        $this->command->info('User 1 referral network created with 10 Level 1 and 15 Level 2 referrals');
    }
    
    private function createAdditionalUsers()
    {
        $this->command->info('Creating additional users...');
        
        $additionalUsers = [
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'en',
                'wallet_address' => '0x' . str_pad(dechex(rand(1000000000, 9999999999)), 40, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),

            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'es',
                'wallet_address' => '0x' . str_pad(dechex(rand(1000000000, 9999999999)), 40, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),

            ],
            [
                'name' => 'Emma Davis',
                'email' => 'emma.davis@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'language' => 'fr',
                'wallet_address' => '0x' . str_pad(dechex(rand(1000000000, 9999999999)), 40, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),

            ],
        ];
        
        foreach ($additionalUsers as $userData) {
            $user = User::create($userData);
            
            // Create subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_name' => rand(0, 1) ? 'Basic' : 'Premium',
                'start_date' => now()->subDays(rand(1, 60)),
                'end_date' => now()->addDays(rand(1, 300)),
                'status' => 'active',
            ]);
            
            // Create transaction
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $subscription->plan_name === 'Premium' ? 99.99 : 49.99,
                'currency' => 'USD',
                'gateway' => rand(0, 1) ? 'credit_card' : 'crypto',
                'status' => 'completed',
                'transaction_hash' => '0x' . strtoupper(uniqid()),
                'notes' => "Subscription payment for {$subscription->plan_name}",
            ]);
        }
        
        $this->command->info('Created ' . count($additionalUsers) . ' additional users');
    }
    
    private function createTransactionData()
    {
        $this->command->info('Creating comprehensive transaction data...');
        
        $users = User::all();
        $gateways = ['credit_card', 'crypto', 'paypal', 'bank_transfer'];
        $currencies = ['USD', 'EUR', 'BTC', 'ETH'];
        $statuses = ['completed', 'pending', 'failed', 'refunded'];
        
        foreach ($users as $user) {
            // Create 2-5 transactions per user
            $transactionCount = rand(2, 5);
            
            for ($i = 0; $i < $transactionCount; $i++) {
                $amount = rand(10, 200);
                $gateway = $gateways[array_rand($gateways)];
                $currency = $currencies[array_rand($currencies)];
                $status = $statuses[array_rand($statuses)];
                
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'currency' => $currency,
                    'gateway' => $gateway,
                    'status' => $status,
                    'transaction_hash' => '0x' . strtoupper(uniqid()),
                    'notes' => "Transaction #" . ($i + 1) . " for {$user->name}",
                    'created_at' => now()->subDays(rand(1, 365)),
                ]);
            }
        }
        
        $this->command->info('Created comprehensive transaction data for all users');
    }
    
    private function createCommissionData()
    {
        $this->command->info('Creating commission data...');
        
        $referrals = Referral::with(['referrer', 'referredUser'])->get();
        
        foreach ($referrals as $referral) {
            $transaction = Transaction::where('user_id', $referral->referred_user_id)->first();
            
            if ($transaction) {
                $commissionAmount = $transaction->amount * (0.10 / $referral->level); // Decreasing commission by level
                
                Commission::create([
                    'earner_user_id' => $referral->user_id,
                    'source_user_id' => $referral->referred_user_id,
                    'transaction_id' => $transaction->id,
                    'level' => $referral->level,
                    'amount' => $commissionAmount,
                    'month' => $transaction->created_at->format('Y-m'),
                    'eligibility' => rand(0, 1) ? 'eligible' : 'ineligible',
                    'payout_status' => ['pending', 'paid', 'void'][array_rand(['pending', 'paid', 'void'])],
                ]);
            }
        }
        
        $this->command->info('Created commission data for all referrals');
    }
    
    private function createMagazineData()
    {
        $this->command->info('Creating magazine data...');
        
        $magazines = [
            [
                'title' => 'Tech Innovation Monthly',
                'description' => 'Latest trends in technology and innovation',
                'category' => 'Technology',
                'language_code' => 'en',
                'file_path' => 'magazines/tech-innovation-monthly.pdf',
                'file_name' => 'tech-innovation-monthly.pdf',
                'file_size' => 2048000,
                'mime_type' => 'application/pdf',
                'uploaded_by_admin_id' => 1,
                'status' => 'active',
            ],
            [
                'title' => 'Business Finance Weekly',
                'description' => 'Weekly insights into business and finance',
                'category' => 'Business',
                'language_code' => 'en',
                'file_path' => 'magazines/business-finance-weekly.pdf',
                'file_name' => 'business-finance-weekly.pdf',
                'file_size' => 1536000,
                'mime_type' => 'application/pdf',
                'uploaded_by_admin_id' => 1,
                'status' => 'active',
            ],
            [
                'title' => 'Health & Wellness Guide',
                'description' => 'Comprehensive health and wellness information',
                'category' => 'Health',
                'language_code' => 'en',
                'file_path' => 'magazines/health-wellness-guide.pdf',
                'file_name' => 'health-wellness-guide.pdf',
                'file_size' => 3072000,
                'mime_type' => 'application/pdf',
                'uploaded_by_admin_id' => 1,
                'status' => 'active',
            ],
            [
                'title' => 'Innovación Tecnológica Mensual',
                'description' => 'Últimas tendencias en tecnología e innovación',
                'category' => 'Technology',
                'language_code' => 'es',
                'file_path' => 'magazines/innovacion-tecnologica-mensual.pdf',
                'file_name' => 'innovacion-tecnologica-mensual.pdf',
                'file_size' => 2048000,
                'mime_type' => 'application/pdf',
                'uploaded_by_admin_id' => 1,
                'status' => 'active',
            ],
            [
                'title' => 'Guide Santé & Bien-être',
                'description' => 'Informations complètes sur la santé et le bien-être',
                'category' => 'Health',
                'language_code' => 'fr',
                'file_path' => 'magazines/guide-sante-bien-etre.pdf',
                'file_name' => 'guide-sante-bien-etre.pdf',
                'file_size' => 2560000,
                'mime_type' => 'application/pdf',
                'uploaded_by_admin_id' => 1,
                'status' => 'active',
            ],
        ];
        
        foreach ($magazines as $magazineData) {
            Magazine::create($magazineData);
        }
        
        $this->command->info('Created ' . count($magazines) . ' magazines');
    }
    
    private function createEmailData()
    {
        $this->command->info('Creating email system data...');
        
        // Create email templates
        $templates = [
            [
                'name' => 'Welcome Email',
                'subject' => 'Welcome to Net On You!',
                'content' => '<h1>Welcome {{user_name}}!</h1><p>Thank you for joining Net On You. Your subscription is now active.</p>',
                'language' => 'en',
                'category' => 'welcome',
                'is_active' => true,
            ],
            [
                'name' => 'Payment Confirmation',
                'subject' => 'Payment Confirmed - Net On You',
                'content' => '<h1>Payment Confirmed</h1><p>Your payment of ${{amount}} has been processed successfully.</p>',
                'language' => 'en',
                'category' => 'payment',
                'is_active' => true,
            ],
            [
                'name' => 'Commission Notification',
                'subject' => 'New Commission Earned!',
                'content' => '<h1>Commission Earned</h1><p>You have earned ${{commission_amount}} in commissions this month.</p>',
                'language' => 'en',
                'category' => 'commission',
                'is_active' => true,
            ],
        ];
        
        foreach ($templates as $templateData) {
            EmailTemplate::create($templateData);
        }
        
        // Create email logs
        $users = User::take(20)->get();
        foreach ($users as $user) {
            EmailLog::create([
                'user_id' => $user->id,
                'template_name' => 'Welcome Email',
                'subject' => 'Welcome to Net On You!',
                'status' => 'sent',
                'sent_at' => now()->subDays(rand(1, 30)),
            ]);
        }
        
        $this->command->info('Created email templates and logs');
    }
    
    private function createLanguageData()
    {
        $this->command->info('Creating language and translation data...');
        
        // Create languages
        $languages = [
            ['name' => 'English', 'code' => 'en', 'flag' => 'us', 'is_default' => true, 'is_active' => true],
            ['name' => 'Spanish', 'code' => 'es', 'flag' => 'es', 'is_default' => false, 'is_active' => true],
            ['name' => 'French', 'code' => 'fr', 'flag' => 'fr', 'is_default' => false, 'is_active' => true],
            ['name' => 'German', 'code' => 'de', 'flag' => 'de', 'is_default' => false, 'is_active' => true],
        ];
        
        foreach ($languages as $languageData) {
            Language::create($languageData);
        }
        
        // Create translations
        $translations = [
            ['language_code' => 'en', 'key' => 'welcome_message', 'value' => 'Welcome to Net On You!'],
            ['language_code' => 'es', 'key' => 'welcome_message', 'value' => '¡Bienvenido a Net On You!'],
            ['language_code' => 'fr', 'key' => 'welcome_message', 'value' => 'Bienvenue sur Net On You!'],
            ['language_code' => 'de', 'key' => 'welcome_message', 'value' => 'Willkommen bei Net On You!'],
            ['language_code' => 'en', 'key' => 'subscription_active', 'value' => 'Your subscription is active'],
            ['language_code' => 'es', 'key' => 'subscription_active', 'value' => 'Tu suscripción está activa'],
            ['language_code' => 'fr', 'key' => 'subscription_active', 'value' => 'Votre abonnement est actif'],
            ['language_code' => 'de', 'key' => 'subscription_active', 'value' => 'Ihr Abonnement ist aktiv'],
        ];
        
        foreach ($translations as $translationData) {
            Translation::create($translationData);
        }
        
        $this->command->info('Created language and translation data');
    }
    
    private function createContractData()
    {
        $this->command->info('Creating contract data...');
        
        $contracts = [
            [
                'title' => 'Terms of Service',
                'content' => '<h1>Terms of Service</h1><p>By using our service, you agree to these terms...</p>',
                'language' => 'en',
                'is_active' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'content' => '<h1>Privacy Policy</h1><p>We respect your privacy and protect your data...</p>',
                'language' => 'en',
                'is_active' => true,
            ],
            [
                'title' => 'Términos de Servicio',
                'content' => '<h1>Términos de Servicio</h1><p>Al usar nuestro servicio, aceptas estos términos...</p>',
                'language' => 'es',
                'is_active' => true,
            ],
        ];
        
        foreach ($contracts as $contractData) {
            Contract::create($contractData);
        }
        
        $this->command->info('Created contract data');
    }
    
    private function createPayoutData()
    {
        $this->command->info('Creating payout data...');
        
        // Create payout batches
        $batch1 = PayoutBatch::create([
            'batch_name' => 'January 2024 Payouts',
            'status' => 'completed',
            'total_amount' => 1500.00,
            'total_commissions' => 25,
            'created_at' => now()->subMonth(),
        ]);
        
        $batch2 = PayoutBatch::create([
            'batch_name' => 'February 2024 Payouts',
            'status' => 'processing',
            'total_amount' => 2200.00,
            'total_commissions' => 35,
            'created_at' => now()->subDays(15),
        ]);
        
        // Create payout batch items
        $commissions = Commission::where('payout_status', 'paid')->take(10)->get();
        foreach ($commissions as $commission) {
            PayoutBatchItem::create([
                'payout_batch_id' => $batch1->id,
                'commission_id' => $commission->id,
                'user_id' => $commission->earner_user_id,
                'amount' => $commission->amount,
                'status' => 'paid',
            ]);
        }
        
        $pendingCommissions = Commission::where('payout_status', 'pending')->take(15)->get();
        foreach ($pendingCommissions as $commission) {
            PayoutBatchItem::create([
                'payout_batch_id' => $batch2->id,
                'commission_id' => $commission->id,
                'user_id' => $commission->earner_user_id,
                'amount' => $commission->amount,
                'status' => 'pending',
            ]);
        }
        
        $this->command->info('Created payout data with batches and items');
    }
    
    private function createAdminData()
    {
        $this->command->info('Creating admin data...');
        
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@netonyou.com',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'status' => 'active',
            ],
            [
                'name' => 'Content Manager',
                'email' => 'content@netonyou.com',
                'password' => Hash::make('content123'),
                'role' => 'content_manager',
                'status' => 'active',
            ],
            [
                'name' => 'Support Manager',
                'email' => 'support@netonyou.com',
                'password' => Hash::make('support123'),
                'role' => 'support_manager',
                'status' => 'active',
            ],
        ];
        
        foreach ($admins as $adminData) {
            Admin::create($adminData);
        }
        
        $this->command->info('Created admin accounts');
    }
    
    private function createSettingsData()
    {
        $this->command->info('Creating settings data...');
        
        $settings = [
            ['key' => 'site_name', 'value' => 'Net On You', 'type' => 'string'],
            ['key' => 'site_email', 'value' => 'info@netonyou.com', 'type' => 'string'],
            ['key' => 'commission_rate_level_1', 'value' => '10', 'type' => 'number'],
            ['key' => 'commission_rate_level_2', 'value' => '5', 'type' => 'number'],
            ['key' => 'commission_rate_level_3', 'value' => '2', 'type' => 'number'],
            ['key' => 'minimum_payout_amount', 'value' => '50', 'type' => 'number'],
            ['key' => 'subscription_basic_price', 'value' => '39.90', 'type' => 'number'],
            ['key' => 'subscription_premium_price', 'value' => '39.90', 'type' => 'number'],
        ];
        
        foreach ($settings as $settingData) {
            Setting::create($settingData);
        }
        
        $this->command->info('Created system settings');
    }
    
    private function createSecurityData()
    {
        $this->command->info('Creating security data...');
        
        $securityPolicies = [
            [
                'policy_name' => 'Password Policy',
                'policy_type' => 'password',
                'policy_value' => json_encode([
                    'min_length' => 8,
                    'require_uppercase' => true,
                    'require_lowercase' => true,
                    'require_numbers' => true,
                    'require_symbols' => false,
                ]),
                'is_active' => true,
            ],
            [
                'policy_name' => 'Session Policy',
                'policy_type' => 'session',
                'policy_value' => json_encode([
                    'timeout_minutes' => 120,
                    'max_concurrent_sessions' => 3,
                ]),
                'is_active' => true,
            ],
        ];
        
        foreach ($securityPolicies as $policyData) {
            SecurityPolicy::create($policyData);
        }
        
        $this->command->info('Created security policies');
    }
    
    private function createSystemData()
    {
        $this->command->info('Creating system data...');
        
        // Create scheduled commands
        $commands = [
            [
                'command_name' => 'Process Payments',
                'command_class' => 'ProcessPaymentsCommand',
                'schedule' => '0 */6 * * *', // Every 6 hours
                'is_active' => true,
                'description' => 'Process pending payments',
            ],
            [
                'command_name' => 'Calculate Commissions',
                'command_class' => 'CalculateCommissionsCommand',
                'schedule' => '0 0 * * *', // Daily at midnight
                'is_active' => true,
                'description' => 'Calculate monthly commissions',
            ],
            [
                'command_name' => 'Send Email Notifications',
                'command_class' => 'SendEmailNotificationsCommand',
                'schedule' => '0 */2 * * *', // Every 2 hours
                'is_active' => true,
                'description' => 'Send scheduled email notifications',
            ],
        ];
        
        foreach ($commands as $commandData) {
            ScheduledCommand::create($commandData);
        }
        
        // Create command logs
        foreach ($commands as $command) {
            CommandLog::create([
                'command_name' => $command['command_name'],
                'status' => 'completed',
                'execution_time' => rand(1, 30),
                'output' => 'Command executed successfully',
                'executed_at' => now()->subHours(rand(1, 24)),
            ]);
        }
        
        // Create audit logs
        $users = User::take(10)->get();
        foreach ($users as $user) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'ip_address' => '192.168.1.' . rand(1, 255),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }
        
        // Create report cache
        ReportCache::create([
            'report_type' => 'monthly_revenue',
            'report_data' => json_encode([
                'total_revenue' => 15000.00,
                'subscription_revenue' => 12000.00,
                'commission_payouts' => 3000.00,
            ]),
            'generated_at' => now()->subHours(6),
            'expires_at' => now()->addHours(18),
        ]);
        
        $this->command->info('Created system data (commands, logs, cache)');
    }
}
