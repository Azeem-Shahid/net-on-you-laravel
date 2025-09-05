<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subscription;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking subscription expiry...');
        
        try {
            $now = now();
            
            // Check subscriptions expiring soon (within 7 days)
            $expiringSoon = Subscription::where('status', 'active')
                ->where('end_date', '>', $now)
                ->where('end_date', '<=', $now->copy()->addDays(7))
                ->with('user')
                ->get();
            
            $this->info("Found {$expiringSoon->count()} subscriptions expiring soon");
            
            foreach ($expiringSoon as $subscription) {
                $this->sendExpiryWarning($subscription);
            }
            
            // Check expired subscriptions (past grace period)
            $expired = Subscription::where('status', 'active')
                ->where('end_date', '<', $now->copy()->subDays(7))
                ->with('user')
                ->get();
            
            $this->info("Found {$expired->count()} expired subscriptions");
            
            foreach ($expired as $subscription) {
                $this->handleExpiredSubscription($subscription);
            }
            
            // Check grace period subscriptions
            $gracePeriod = Subscription::where('status', 'active')
                ->where('end_date', '<=', $now)
                ->where('end_date', '>', $now->copy()->subDays(7))
                ->with('user')
                ->get();
            
            $this->info("Found {$gracePeriod->count()} subscriptions in grace period");
            
            foreach ($gracePeriod as $subscription) {
                $this->sendGracePeriodNotification($subscription);
            }
            
            $this->info('Subscription expiry check completed successfully');
            return 0;
            
        } catch (\Exception $e) {
            Log::error("Subscription expiry check failed: " . $e->getMessage());
            $this->error("Command failed: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Send expiry warning to user
     */
    private function sendExpiryWarning(Subscription $subscription)
    {
        $user = $subscription->user;
        $daysLeft = $subscription->end_date->diffInDays(now());
        
        $this->line("Sending expiry warning to {$user->email} ({$daysLeft} days left)");
        
        try {
            $template = $this->getOrCreateTemplate('subscription_expiry_warning');
            
            $data = [
                'user_name' => $user->name,
                'days_left' => $daysLeft,
                'expiry_date' => $subscription->end_date->format('M d, Y'),
                'renewal_url' => route('payment.checkout')
            ];
            
            $this->sendEmail($user, $template, $data, 'subscription_expiry_warning');
            
        } catch (\Exception $e) {
            Log::error("Failed to send expiry warning to user {$user->id}: " . $e->getMessage());
        }
    }
    
    /**
     * Handle expired subscription
     */
    private function handleExpiredSubscription(Subscription $subscription)
    {
        $user = $subscription->user;
        
        $this->line("Handling expired subscription for {$user->email}");
        
        try {
            // Update subscription status
            $subscription->update(['status' => 'expired']);
            
            // Send expired notification
            $template = $this->getOrCreateTemplate('subscription_expired');
            
            $data = [
                'user_name' => $user->name,
                'expiry_date' => $subscription->end_date->format('M d, Y'),
                'renewal_url' => route('payment.checkout')
            ];
            
            $this->sendEmail($user, $template, $data, 'subscription_expired');
            
        } catch (\Exception $e) {
            Log::error("Failed to handle expired subscription for user {$user->id}: " . $e->getMessage());
        }
    }
    
    /**
     * Send grace period notification
     */
    private function sendGracePeriodNotification(Subscription $subscription)
    {
        $user = $subscription->user;
        $daysInGrace = $subscription->end_date->diffInDays(now());
        
        $this->line("Sending grace period notification to {$user->email} (day {$daysInGrace} of grace period)");
        
        try {
            $template = $this->getOrCreateTemplate('subscription_grace_period');
            
            $data = [
                'user_name' => $user->name,
                'grace_days_left' => 7 - $daysInGrace,
                'expiry_date' => $subscription->end_date->format('M d, Y'),
                'renewal_url' => route('payment.checkout')
            ];
            
            $this->sendEmail($user, $template, $data, 'subscription_grace_period');
            
        } catch (\Exception $e) {
            Log::error("Failed to send grace period notification to user {$user->id}: " . $e->getMessage());
        }
    }
    
    /**
     * Get or create email template
     */
    private function getOrCreateTemplate($templateName)
    {
        $template = EmailTemplate::where('name', $templateName)->first();
        
        if (!$template) {
            $template = EmailTemplate::create([
                'name' => $templateName,
                'language' => 'en',
                'subject' => $this->getDefaultSubject($templateName),
                'body' => $this->getDefaultBody($templateName),
                'variables' => $this->getDefaultVariables($templateName),
                'created_by_admin_id' => 1
            ]);
        }
        
        return $template;
    }
    
    /**
     * Get default subject for template
     */
    private function getDefaultSubject($templateName)
    {
        $subjects = [
            'subscription_expiry_warning' => 'Your subscription expires soon',
            'subscription_expired' => 'Your subscription has expired',
            'subscription_grace_period' => 'Grace period reminder - Renew your subscription'
        ];
        
        return $subjects[$templateName] ?? 'Subscription notification';
    }
    
    /**
     * Get default body for template
     */
    private function getDefaultBody($templateName)
    {
        $bodies = [
            'subscription_expiry_warning' => "
Dear {user_name},

Your subscription will expire in {days_left} days on {expiry_date}.

To maintain uninterrupted access to our magazines, please renew your subscription.

Renew now: {renewal_url}

Best regards,
NetOnYou Team
            ",
            'subscription_expired' => "
Dear {user_name},

Your subscription expired on {expiry_date}.

You currently have no access to our magazines. To restore access, please renew your subscription.

Renew now: {renewal_url}

Best regards,
NetOnYou Team
            ",
            'subscription_grace_period' => "
Dear {user_name},

You are currently in the grace period after your subscription expired on {expiry_date}.

You have {grace_days_left} days remaining in the grace period before your account is blocked.

To restore full access, please renew your subscription now.

Renew now: {renewal_url}

Best regards,
NetOnYou Team
            "
        ];
        
        return $bodies[$templateName] ?? 'Subscription notification';
    }
    
    /**
     * Get default variables for template
     */
    private function getDefaultVariables($templateName)
    {
        $variables = [
            'subscription_expiry_warning' => ['user_name', 'days_left', 'expiry_date', 'renewal_url'],
            'subscription_expired' => ['user_name', 'expiry_date', 'renewal_url'],
            'subscription_grace_period' => ['user_name', 'grace_days_left', 'expiry_date', 'renewal_url']
        ];
        
        return $variables[$templateName] ?? ['user_name'];
    }
    
    /**
     * Send email to user
     */
    private function sendEmail(User $user, EmailTemplate $template, array $data, string $logType)
    {
        $subject = $template->replaceVariables($data)['subject'];
        $body = $template->replaceVariables($data)['body'];
        
        // Send email
        Mail::raw($body, function($message) use ($user, $subject) {
            $message->to($user->email)
                    ->subject($subject);
        });
        
        // Log email
        EmailLog::create([
            'template_name' => $template->name,
            'recipient_email' => $user->email,
            'recipient_type' => 'user',
            'recipient_id' => $user->id,
            'subject' => $subject,
            'body' => $body,
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
}

