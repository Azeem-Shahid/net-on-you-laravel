<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\NotificationSetting;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Display a listing of campaigns
     */
    public function index()
    {
        // For now, we'll show recent bulk email activities
        // In a full implementation, you might want a campaigns table
        $recentBulkEmails = DB::table('email_logs')
            ->select('template_name', 'created_at', DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('template_name', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.campaigns.index', compact('recentBulkEmails'));
    }

    /**
     * Show the form for creating a new campaign
     */
    public function create()
    {
        $templates = EmailTemplate::where('language', 'en')->get();
        $userCount = User::count();
        $marketingOptInCount = NotificationSetting::where('marketing_opt_in', true)->count();

        return view('admin.campaigns.create', compact('templates', 'userCount', 'marketingOptInCount'));
    }

    /**
     * Store a newly created campaign
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:email_templates,id',
            'subject' => 'required|string|max:191',
            'recipient_type' => 'required|in:all_users,marketing_opt_in,custom_selection',
            'custom_user_ids' => 'required_if:recipient_type,custom_selection|array',
            'custom_user_ids.*' => 'exists:users,id',
            'test_email' => 'nullable|email',
            'send_test' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $template = EmailTemplate::findOrFail($request->template_id);

        // Send test email if requested
        if ($request->send_test && $request->test_email) {
            try {
                $result = $this->emailService->sendTestEmail(
                    $template->name,
                    $request->test_email,
                    $this->getSampleData($template),
                    $template->language
                );

                if (!$result) {
                    return back()->with('error', 'Test email failed to send. Please check your email configuration.')->withInput();
                }

                return back()->with('success', 'Test email sent successfully to ' . $request->test_email)->withInput();
            } catch (Exception $e) {
                return back()->with('error', 'Test email failed: ' . $e->getMessage())->withInput();
            }
        }

        // Get recipient list
        $recipientIds = $this->getRecipientIds($request);

        if (empty($recipientIds)) {
            return back()->with('error', 'No recipients found for this campaign.')->withInput();
        }

        // Send bulk email
        try {
            $result = $this->emailService->sendBulkMarketingEmail(
                $template->name,
                $recipientIds,
                $this->getSampleData($template),
                $template->language
            );

            $message = "Campaign sent successfully. {$result['success']} emails sent, {$result['failed']} failed out of {$result['total']} total recipients.";

            return redirect()->route('admin.campaigns.index')
                ->with('success', $message);

        } catch (Exception $e) {
            return back()->with('error', 'Campaign failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show campaign preview
     */
    public function preview(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'subject' => 'required|string|max:191'
        ]);

        $template = EmailTemplate::findOrFail($request->template_id);
        $sampleData = $this->getSampleData($template);
        $processed = $template->replaceVariables($sampleData);

        return response()->json([
            'subject' => $processed['subject'],
            'body' => $processed['body'],
            'variables' => $template->variables
        ]);
    }

    /**
     * Get recipient count for campaign
     */
    public function getRecipientCount(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:all_users,marketing_opt_in,custom_selection',
            'custom_user_ids' => 'required_if:recipient_type,custom_selection|array',
            'custom_user_ids.*' => 'exists:users,id'
        ]);

        $count = $this->getRecipientIds($request, true);

        return response()->json(['count' => $count]);
    }

    /**
     * Get recipient IDs based on campaign settings
     */
    private function getRecipientIds(Request $request, $countOnly = false)
    {
        $query = User::query();

        switch ($request->recipient_type) {
            case 'all_users':
                // All users
                break;

            case 'marketing_opt_in':
                // Only users who opted in to marketing
                $query->whereHas('notificationSetting', function($q) {
                    $q->where('marketing_opt_in', true);
                });
                break;

            case 'custom_selection':
                // Custom user selection
                $query->whereIn('id', $request->custom_user_ids ?? []);
                break;
        }

        if ($countOnly) {
            return $query->count();
        }

        return $query->pluck('id')->toArray();
    }

    /**
     * Get sample data for template preview
     */
    private function getSampleData(EmailTemplate $template)
    {
        $sampleData = [];

        if (!empty($template->variables)) {
            foreach ($template->variables as $variable) {
                $sampleData[$variable] = $this->getSampleValue($variable);
            }
        }

        return $sampleData;
    }

    /**
     * Get sample value for a variable
     */
    private function getSampleValue($variable)
    {
        return match($variable) {
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'plan' => 'Premium',
            'expiry' => now()->addDays(30)->format('Y-m-d'),
            'amount' => '$29.99',
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
            'reset_link' => url('/reset-password/sample-token'),
            'payout_id' => 'PAY-' . strtoupper(uniqid()),
            'date' => now()->format('Y-m-d H:i:s'),
            'company_name' => 'Net On You',
            'support_email' => 'support@netonyou.com',
            default => 'Sample Value'
        };
    }

    /**
     * Get user statistics for campaign planning
     */
    public function getUserStats()
    {
        $stats = [
            'total_users' => User::count(),
            'marketing_opt_in' => NotificationSetting::where('marketing_opt_in', true)->count(),
            'marketing_opt_out' => NotificationSetting::where('marketing_opt_in', false)->count(),
            'no_preference' => User::whereDoesntHave('notificationSetting')->count(),
            'by_language' => NotificationSetting::select('language_preference', DB::raw('count(*) as count'))
                ->groupBy('language_preference')
                ->pluck('count', 'language_preference')
                ->toArray()
        ];

        return response()->json($stats);
    }
}
