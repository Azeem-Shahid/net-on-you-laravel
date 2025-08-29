<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\Admin;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmailTemplateController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Display a listing of email templates
     */
    public function index(Request $request)
    {
        $query = EmailTemplate::with(['createdByAdmin', 'updatedByAdmin']);

        // Filter by language
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        // Filter by template name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $templates = $query->orderBy('name')
                          ->orderBy('language')
                          ->paginate(15);

        $languages = EmailTemplate::distinct()->pluck('language')->sort();
        $templateNames = EmailTemplate::distinct()->pluck('name')->sort();

        return view('admin.email-templates.index', compact('templates', 'languages', 'templateNames'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        $languages = ['en', 'ur', 'fr', 'es', 'ar', 'hi', 'bn', 'pt', 'ru', 'zh'];
        $commonVariables = [
            'name', 'email', 'plan', 'expiry', 'amount', 'transaction_id', 
            'reset_link', 'payout_id', 'date', 'company_name', 'support_email'
        ];

        return view('admin.email-templates.create', compact('languages', 'commonVariables'));
    }

    /**
     * Get language name for display
     */
    private function getLanguageName($code)
    {
        $names = [
            'en' => 'English',
            'ur' => 'Urdu',
            'fr' => 'French',
            'es' => 'Spanish',
            'ar' => 'Arabic',
            'hi' => 'Hindi',
            'bn' => 'Bengali',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'zh' => 'Chinese'
        ];

        return $names[$code] ?? strtoupper($code);
    }

    /**
     * Store a newly created template
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'language' => 'required|string|max:10',
            'subject' => 'required|string|max:191',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'variables.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if template with same name and language already exists
        $existingTemplate = EmailTemplate::where('name', $request->name)
            ->where('language', $request->language)
            ->first();

        if ($existingTemplate) {
            return back()->withErrors(['name' => 'A template with this name and language already exists.'])->withInput();
        }

        $admin = Auth::guard('admin')->user();

        $template = EmailTemplate::create([
            'name' => $request->name,
            'language' => $request->language,
            'subject' => $request->subject,
            'body' => $request->body,
            'variables' => $request->variables ?? [],
            'created_by_admin_id' => $admin->id,
            'updated_by_admin_id' => $admin->id,
        ]);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    /**
     * Display the specified template
     */
    public function show(EmailTemplate $emailTemplate)
    {
        $emailTemplate->load(['createdByAdmin', 'updatedByAdmin']);
        
        // Get email logs for this template
        $emailLogs = $emailTemplate->emailLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.email-templates.show', compact('emailTemplate', 'emailLogs'));
    }

    /**
     * Show the form for editing the specified template
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        $languages = ['en', 'ur', 'fr', 'es', 'ar', 'hi', 'bn', 'pt', 'ru', 'zh'];
        $commonVariables = [
            'name', 'email', 'plan', 'expiry', 'amount', 'transaction_id', 
            'reset_link', 'payout_id', 'date', 'company_name', 'support_email'
        ];

        return view('admin.email-templates.edit', compact('emailTemplate', 'languages', 'commonVariables'));
    }

    /**
     * Update the specified template
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'language' => 'required|string|max:10',
            'subject' => 'required|string|max:191',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'variables.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if template with same name and language already exists (excluding current)
        $existingTemplate = EmailTemplate::where('name', $request->name)
            ->where('language', $request->language)
            ->where('id', '!=', $emailTemplate->id)
            ->first();

        if ($existingTemplate) {
            return back()->withErrors(['name' => 'A template with this name and language already exists.'])->withInput();
        }

        $admin = Auth::guard('admin')->user();

        $emailTemplate->update([
            'name' => $request->name,
            'language' => $request->language,
            'subject' => $request->subject,
            'body' => $request->body,
            'variables' => $request->variables ?? [],
            'updated_by_admin_id' => $admin->id,
        ]);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    /**
     * Remove the specified template
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        // Check if template is being used
        $usageCount = $emailTemplate->emailLogs()->count();
        
        if ($usageCount > 0) {
            return back()->with('error', 'Cannot delete template. It has been used ' . $usageCount . ' times.');
        }

        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    /**
     * Send test email
     */
    public function sendTest(Request $request, EmailTemplate $emailTemplate)
    {
        $validator = Validator::make($request->all(), [
            'test_email' => 'required|email',
            'test_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $result = $this->emailService->sendTestEmail(
                $emailTemplate->name,
                $request->test_email,
                $request->test_data ?? [],
                $emailTemplate->language
            );

            if ($result) {
                return back()->with('success', 'Test email sent successfully to ' . $request->test_email);
            } else {
                return back()->with('error', 'Failed to send test email.');
            }

        } catch (Exception $e) {
            return back()->with('error', 'Error sending test email: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate template
     */
    public function duplicate(EmailTemplate $emailTemplate)
    {
        $newTemplate = $emailTemplate->replicate();
        $newTemplate->name = $emailTemplate->name . '_copy';
        $newTemplate->language = 'en'; // Default to English
        $newTemplate->created_by_admin_id = Auth::guard('admin')->id();
        $newTemplate->updated_by_admin_id = Auth::guard('admin')->id();
        $newTemplate->save();

        return redirect()->route('admin.email-templates.edit', $newTemplate)
            ->with('success', 'Template duplicated successfully. Please review and save.');
    }
}
