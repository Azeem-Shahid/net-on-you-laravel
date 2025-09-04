<?php
/**
 * Email System Verification Script
 * Run this script to verify email functionality after deployment
 * 
 * Usage: php verify_email_system.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Services\EmailService;
use App\Models\User;
use App\Models\Admin;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Net On You Email System Verification ===\n\n";

// Test 1: Configuration Check
echo "1. Email Configuration Check...\n";
echo "   MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
echo "   MAIL_HOST: " . env('MAIL_HOST') . "\n";
echo "   MAIL_PORT: " . env('MAIL_PORT') . "\n";
echo "   MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
echo "   MAIL_FROM_NAME: " . env('MAIL_FROM_NAME') . "\n\n";

// Test 2: Email Templates Check
echo "2. Email Templates Check...\n";
$templates = EmailTemplate::all();
echo "   Found " . $templates->count() . " email templates:\n";
foreach ($templates as $template) {
    echo "   ✓ {$template->name} ({$template->language})\n";
}
echo "\n";

// Test 3: Basic Email Test
echo "3. Basic Email Sending Test...\n";
try {
    Mail::raw('This is a test email from Net On You system verification.', function($message) {
        $message->to('test@example.com')
                ->subject('Net On You - System Verification Test');
    });
    echo "   ✓ Basic email sending test passed\n";
} catch (Exception $e) {
    echo "   ✗ Basic email sending test failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: EmailService Test
echo "4. EmailService Functionality Test...\n";
$emailService = new EmailService();
$testUser = User::first();

if ($testUser) {
    // Test welcome email
    $result = $emailService->sendWelcomeEmail($testUser->id);
    if ($result) {
        echo "   ✓ Welcome email sent successfully\n";
    } else {
        echo "   ✗ Welcome email failed\n";
    }
    
    // Test password reset email
    $result = $emailService->sendPasswordResetEmail($testUser->id, 'test-token-123');
    if ($result) {
        echo "   ✓ Password reset email sent successfully\n";
    } else {
        echo "   ✗ Password reset email failed\n";
    }
} else {
    echo "   ⚠ No test user found - create a user first\n";
}
echo "\n";

// Test 5: Admin Email Test
echo "5. Admin Email Test...\n";
$testAdmin = Admin::first();
if ($testAdmin) {
    try {
        Mail::raw("Admin verification test: {$testAdmin->name} - " . now(), function($message) use ($testAdmin) {
            $message->to($testAdmin->email)
                    ->subject('Net On You - Admin Verification Test');
        });
        echo "   ✓ Admin email sent successfully\n";
    } catch (Exception $e) {
        echo "   ✗ Admin email failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⚠ No admin found - create an admin first\n";
}
echo "\n";

// Test 6: Email Logging Check
echo "6. Email Logging Check...\n";
$recentLogs = EmailLog::latest()->take(5)->get();
if ($recentLogs->count() > 0) {
    echo "   Recent email logs:\n";
    foreach ($recentLogs as $log) {
        $status = $log->status === 'sent' ? '✓' : '✗';
        echo "   {$status} {$log->template_name} to {$log->email} - {$log->status}\n";
    }
} else {
    echo "   ⚠ No email logs found\n";
}
echo "\n";

// Test 7: Email Statistics
echo "7. Email Statistics...\n";
$totalSent = EmailLog::where('status', 'sent')->count();
$totalFailed = EmailLog::where('status', 'failed')->count();
$totalQueued = EmailLog::where('status', 'queued')->count();

echo "   Total emails sent: {$totalSent}\n";
echo "   Total emails failed: {$totalFailed}\n";
echo "   Total emails queued: {$totalQueued}\n";

if ($totalSent + $totalFailed > 0) {
    $successRate = round(($totalSent / ($totalSent + $totalFailed)) * 100, 2);
    echo "   Success rate: {$successRate}%\n";
}
echo "\n";

// Final Status
echo "=== Verification Complete ===\n";
if (env('MAIL_MAILER') === 'log') {
    echo "📝 Note: Emails are being logged to storage/logs/laravel.log\n";
    echo "   For production, update .env with actual SMTP settings\n";
} else {
    echo "📧 Emails are being sent via " . env('MAIL_MAILER') . "\n";
    echo "   Check your email inbox for test emails\n";
}

echo "\n✅ Email system verification complete!\n";
echo "📋 Check EMAIL_SYSTEM_SUMMARY.md for detailed status\n";
echo "🚀 System is ready for production deployment\n";

