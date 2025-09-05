<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\EmailLog;
use App\Models\User;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailService
{
    /**
     * Send email using a template
     */
    public function sendTemplateEmail($templateName, $userId, $data = [], $language = 'en')
    {
        try {
            // Get user
            $user = User::find($userId);
            if (!$user) {
                throw new Exception("User not found: {$userId}");
            }

            // Check notification settings
            $notificationSetting = NotificationSetting::where('user_id', $userId)->first();
            if ($notificationSetting && !$notificationSetting->isMarketingOptIn()) {
                // Log that email was skipped due to opt-out
                $this->logEmail($templateName, $userId, $user->email, 'Skipped - Marketing Opt-out', '', 'failed', 'User opted out of marketing emails');
                return false;
            }

            // Get template
            $template = EmailTemplate::where('name', $templateName)
                ->where('language', $language)
                ->first();

            if (!$template) {
                // Fallback to English if language not found
                $template = EmailTemplate::where('name', $templateName)
                    ->where('language', 'en')
                    ->first();
            }

            if (!$template) {
                throw new Exception("Template not found: {$templateName}");
            }

            // Replace variables
            $processed = $template->replaceVariables($data);
            
            // Create email log entry
            $emailLog = $this->logEmail(
                $templateName,
                $userId,
                $user->email,
                $processed['subject'],
                $processed['body'],
                'queued'
            );

            // Send email
            $this->sendEmail($user->email, $processed['subject'], $processed['body'], $emailLog);

            return true;

        } catch (Exception $e) {
            Log::error('Email sending failed', [
                'template' => $templateName,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            // Log the failure
            $this->logEmail(
                $templateName,
                $userId,
                $user->email ?? 'unknown',
                'Failed',
                '',
                'failed',
                $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Send bulk marketing email
     */
    public function sendBulkMarketingEmail($templateName, $userIds, $data = [], $language = 'en')
    {
        $successCount = 0;
        $failureCount = 0;

        foreach ($userIds as $userId) {
            try {
                $result = $this->sendTemplateEmail($templateName, $userId, $data, $language);
                if ($result) {
                    $successCount++;
                } else {
                    $failureCount++;
                }
            } catch (Exception $e) {
                $failureCount++;
                Log::error('Bulk email failed for user', [
                    'user_id' => $userId,
                    'template' => $templateName,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'success' => $successCount,
            'failed' => $failureCount,
            'total' => count($userIds)
        ];
    }

    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail($userId)
    {
        $user = User::find($userId);
        if (!$user) return false;

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'plan' => 'Free',
            'expiry' => now()->addDays(30)->format('Y-m-d')
        ];

        return $this->sendTemplateEmail('welcome_email', $userId, $data, $user->language ?? 'en');
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($userId, $resetToken)
    {
        $user = User::find($userId);
        if (!$user) return false;

        $data = [
            'name' => $user->name,
            'reset_link' => url("/reset-password/{$resetToken}")
        ];

        return $this->sendTemplateEmail('password_reset', $userId, $data);
    }

    /**
     * Send payment confirmation email
     */
    public function sendPaymentConfirmationEmail($userId, $transaction)
    {
        $user = User::find($userId);
        if (!$user) return false;

        $data = [
            'name' => $user->name,
            'amount' => $transaction->amount,
            'plan' => $transaction->plan_name ?? 'Premium',
            'transaction_id' => $transaction->id,
            'date' => $transaction->created_at->format('Y-m-d H:i:s')
        ];

        return $this->sendTemplateEmail('payment_confirmation', $userId, $data);
    }

    /**
     * Send commission payout email
     */
    public function sendCommissionPayoutEmail($userId, $payout)
    {
        $user = User::find($userId);
        if (!$user) return false;

        $data = [
            'name' => $user->name,
            'amount' => $payout->amount,
            'payout_id' => $payout->id,
            'date' => $payout->created_at->format('Y-m-d H:i:s')
        ];

        return $this->sendTemplateEmail('commission_payout', $userId, $data);
    }

    /**
     * Send test email
     */
    public function sendTestEmail($templateName, $testEmail, $data = [], $language = 'en')
    {
        try {
            $template = EmailTemplate::where('name', $templateName)
                ->where('language', $language)
                ->first();

            if (!$template) {
                throw new Exception("Template not found: {$templateName}");
            }

            $processed = $template->replaceVariables($data);
            
            // Send directly without logging
            $this->sendEmail($testEmail, $processed['subject'], $processed['body']);

            return true;

        } catch (Exception $e) {
            Log::error('Test email failed', [
                'template' => $templateName,
                'email' => $testEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Actually send the email
     */
    private function sendEmail($to, $subject, $body, $emailLog = null)
    {
        try {
            // For now, we'll use Laravel's default mail system
            // In production, you might want to use a service like SendGrid, Mailgun, etc.
            Mail::raw($body, function($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject);
            });

            // Update log status if provided
            if ($emailLog) {
                $emailLog->update([
                    'status' => 'sent',
                    'sent_at' => now()
                ]);
            }

            return true;

        } catch (Exception $e) {
            // Update log status if provided
            if ($emailLog) {
                $emailLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }

            throw $e;
        }
    }

    /**
     * Log email attempt
     */
    private function logEmail($templateName, $userId, $email, $subject, $body, $status, $errorMessage = null)
    {
        return EmailLog::create([
            'template_name' => $templateName,
            'user_id' => $userId,
            'email' => $email,
            'subject' => $subject,
            'body_snapshot' => $body,
            'status' => $status,
            'error_message' => $errorMessage,
        ]);
    }
}
