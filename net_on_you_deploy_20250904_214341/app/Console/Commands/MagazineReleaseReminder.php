<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MagazineReleaseReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magazines:release-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder to admins for bimonthly magazine release';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending magazine release reminders to admins...');
        
        try {
            // Get all active admins
            $admins = Admin::where('status', 'active')->get();
            
            if ($admins->isEmpty()) {
                $this->warn('No active admins found');
                return 0;
            }
            
            // Get or create email template
            $template = $this->getOrCreateTemplate();
            
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($admins as $admin) {
                try {
                    $this->sendReminder($admin, $template);
                    $successCount++;
                    $this->line("Sent reminder to: {$admin->email}");
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("Failed to send reminder to {$admin->email}: " . $e->getMessage());
                    Log::error("Magazine release reminder failed for admin {$admin->id}: " . $e->getMessage());
                }
            }
            
            $this->info("Magazine release reminders completed:");
            $this->info("  - Success: {$successCount}");
            $this->info("  - Errors: {$errorCount}");
            
            return 0;
            
        } catch (\Exception $e) {
            Log::error("Magazine release reminder command failed: " . $e->getMessage());
            $this->error("Command failed: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Get or create the magazine release reminder template
     */
    private function getOrCreateTemplate()
    {
        $template = EmailTemplate::where('name', 'magazine_release_reminder')->first();
        
        if (!$template) {
            $template = EmailTemplate::create([
                'name' => 'magazine_release_reminder',
                'language' => 'en',
                'subject' => 'Magazine Release Reminder - Bimonthly Issue Due',
                'body' => $this->getDefaultTemplate(),
                'variables' => ['admin_name', 'current_month', 'next_release_date'],
                'created_by_admin_id' => 1
            ]);
        }
        
        return $template;
    }
    
    /**
     * Get default email template
     */
    private function getDefaultTemplate()
    {
        return "
Dear {admin_name},

This is a reminder that a new bimonthly magazine issue is due for release.

Current Month: {current_month}
Next Release Date: {next_release_date}

Please ensure the following:
1. Magazine content is prepared and reviewed
2. PDF file is uploaded to the system
3. Magazine is published and made available to subscribers
4. Email notifications are sent to active users

You can manage magazines through the admin dashboard at: " . url('/admin/magazines') . "

Best regards,
NetOnYou System
        ";
    }
    
    /**
     * Send reminder email to admin
     */
    private function sendReminder(Admin $admin, EmailTemplate $template)
    {
        $currentMonth = now()->format('F Y');
        $nextReleaseDate = now()->addMonths(2)->startOfMonth()->format('F 1, Y');
        
        $data = [
            'admin_name' => $admin->name,
            'current_month' => $currentMonth,
            'next_release_date' => $nextReleaseDate
        ];
        
        $subject = $template->replaceVariables($data)['subject'];
        $body = $template->replaceVariables($data)['body'];
        
        // Send email
        Mail::raw($body, function($message) use ($admin, $subject) {
            $message->to($admin->email)
                    ->subject($subject);
        });
        
        // Log email
        EmailLog::create([
            'template_name' => $template->name,
            'recipient_email' => $admin->email,
            'recipient_type' => 'admin',
            'recipient_id' => $admin->id,
            'subject' => $subject,
            'body' => $body,
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
}

