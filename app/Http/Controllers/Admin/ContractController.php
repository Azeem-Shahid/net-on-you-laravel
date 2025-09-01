<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('admin');
    }

    /**
     * Show contracts index page
     */
    public function index()
    {
        // Check permission (super admin or editor can manage contracts)
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->isEditor()) {
            abort(403, 'Unauthorized action.');
        }

        $contracts = Contract::with('acceptances')->orderBy('language')->orderBy('effective_date', 'desc')->get();
        $languages = Language::where('status', 'active')->get();

        return view('admin.contracts.index', compact('contracts', 'languages'));
    }

    /**
     * Show create contract form
     */
    public function create()
    {
        // Check permission (super admin or editor can manage contracts)
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->isEditor()) {
            abort(403, 'Unauthorized action.');
        }

        $languages = Language::where('status', 'active')->get();
        return view('admin.contracts.create', compact('languages'));
    }

    /**
     * Store new contract
     */
    public function store(Request $request)
    {
        // Check permission (super admin or editor can manage contracts)
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->isEditor()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'version' => 'required|string|max:50',
            'language' => 'required|string|exists:languages,code',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'effective_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // If this contract is active, deactivate others of the same language
        if ($request->boolean('is_active')) {
            Contract::where('language', $request->language)
                ->update(['is_active' => false]);
        }

        $contract = Contract::create([
            'version' => $request->version,
            'language' => $request->language,
            'title' => $request->title,
            'content' => $request->content,
            'effective_date' => $request->effective_date,
            'is_active' => $request->boolean('is_active'),
        ]);

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'create_contract',
            'contract',
            $contract->id,
            [
                'version' => $contract->version,
                'language' => $contract->language,
                'title' => $contract->title,
            ]
        );

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contract created successfully.');
    }

    /**
     * Show contract details
     */
    public function show(Contract $contract)
    {
        // Check permission (super admin or editor can manage contracts)
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->isEditor()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.contracts.show', compact('contract'));
    }

    /**
     * Show edit contract form
     */
    public function edit(Contract $contract)
    {
        // Check permission (super admin or editor can manage contracts)
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->isEditor()) {
            abort(403, 'Unauthorized action.');
        }

        $languages = Language::where('status', 'active')->get();
        return view('admin.contracts.edit', compact('contract', 'languages'));
    }

    /**
     * Update contract
     */
    public function update(Request $request, Contract $contract)
    {
        // Check permission (super admin or editor can manage contracts)
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->isEditor()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'version' => 'required|string|max:50',
            'language' => 'required|string|exists:languages,code',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'effective_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldData = $contract->toArray();

        // If this contract is active, deactivate others of the same language
        if ($request->boolean('is_active')) {
            Contract::where('language', $request->language)
                ->where('id', '!=', $contract->id)
                ->update(['is_active' => false]);
        }

        $contract->update([
            'version' => $request->version,
            'language' => $request->language,
            'title' => $request->title,
            'content' => $request->content,
            'effective_date' => $request->effective_date,
            'is_active' => $request->boolean('is_active'),
        ]);

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'update_contract',
            'contract',
            $contract->id,
            [
                'old_data' => $oldData,
                'new_data' => $contract->toArray(),
            ]
        );

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contract updated successfully.');
    }

    /**
     * Delete contract
     */
    public function destroy(Contract $contract)
    {
        // Check permission (super admin or editor can manage contracts)
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->isEditor()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if contract has acceptances
        if ($contract->acceptances()->count() > 0) {
            return back()->with('error', 'Cannot delete contract with existing acceptances.');
        }

        $contractData = $contract->toArray();
        $contract->delete();

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            'delete_contract',
            'contract',
            null,
            [
                'deleted_contract' => $contractData,
            ]
        );

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }

    /**
     * Toggle contract status
     */
    public function toggleStatus(Contract $contract)
    {
        // Check permission (super admin or editor can manage contracts)
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->isEditor()) {
            abort(403, 'Unauthorized action.');
        }

        if ($contract->is_active) {
            $contract->deactivate();
            $message = 'Contract deactivated successfully.';
        } else {
            // Deactivate other contracts of the same language
            Contract::where('language', $contract->language)
                ->where('id', '!=', $contract->id)
                ->update(['is_active' => false]);
            
            $contract->activate();
            $message = 'Contract activated successfully.';
        }

        // Log admin activity
        \App\Models\AdminActivityLog::log(
            auth('admin')->id(),
            $contract->is_active ? 'activate_contract' : 'deactivate_contract',
            'contract',
            $contract->id,
            [
                'status' => $contract->is_active ? 'active' : 'inactive',
            ]
        );

        return back()->with('success', $message);
    }

    /**
     * Import contract from file
     */
    public function import(Request $request)
    {
        // Check permission (super admin or editor can manage contracts)
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->isEditor()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:txt,html,doc,docx|max:2048',
            'language' => 'required|string|exists:languages,code',
            'version' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'effective_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $content = file_get_contents($request->file('file')->getRealPath());
            
            // If this contract is active, deactivate others of the same language
            if ($request->boolean('is_active')) {
                Contract::where('language', $request->language)
                    ->update(['is_active' => false]);
            }

            $contract = Contract::create([
                'version' => $request->version,
                'language' => $request->language,
                'title' => $request->title,
                'content' => $content,
                'effective_date' => $request->effective_date,
                'is_active' => $request->boolean('is_active'),
            ]);

            // Log admin activity
            \App\Models\AdminActivityLog::log(
                auth('admin')->id(),
                'import_contract',
                'contract',
                $contract->id,
                [
                    'version' => $contract->version,
                    'language' => $contract->language,
                    'title' => $contract->title,
                    'file' => $request->file('file')->getClientOriginalName(),
                ]
            );

            return redirect()->route('admin.contracts.index')
                ->with('success', 'Contract imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import contract: ' . $e->getMessage());
        }
    }

    /**
     * Export contract
     */
    public function export(Contract $contract)
    {
        // Check permission (super admin or editor can manage contracts)
        if (!auth('admin')->user()->isSuperAdmin() && !auth('admin')->user()->isEditor()) {
            abort(403, 'Unauthorized action.');
        }

        $filename = "contract_{$contract->language}_{$contract->version}.txt";
        
        return response($contract->content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }


}

